<?php

namespace App\Payment;

use App\Auth\AuthenticatedUser;
use App\Entity\Transaction;
use App\Entity\TransactionCmdEnum;
use App\Entity\TransactionOriginEnum;
use App\Entity\TransactionStatusEnum;
use App\Payment\Bank\Mellat\Link as MellatLink;
use App\Payment\Bank\Mellat\CallbackResponse as MellatCallback;
use App\Payment\Bank\Mellat\Response as MellatResponse;
use App\Payment\Cmd\BankCmd;
use App\Payment\Cmd\PaymentCmd;
use App\Payment\Cmd\PaymentResponseCmd;
use App\Payment\Cmd\ReversalCmd;
use App\Payment\Cmd\SettleCmd;
use App\Payment\Cmd\VerifyCmd;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionMethod;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BankOperatonManager
{
    private const TransactionToCmdMapping = [
        'id' => 'transactionId',
        'parentId' => 'paymentTransactionId',
    ];

    public function __construct(
        private TransactionRepository $transactionRepository,
        private AuthenticatedUser $authenticatedUser,
        private MellatLink $mellatLink,
        private UrlGeneratorInterface $router,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function run(BankCmd $cmd): ?BankCmd
    {
        // get command handler
        $handler = new ($cmd->getRunner())($cmd, $this);

        $this->entityManager->beginTransaction();

        // persist it in database and forge created at and transaction id
        $cmd->forgeLink('Mellat');
        $transaction = $this->initTransactionFromCmd($cmd);
        $this->transactionRepository->add($transaction, true);

        if (is_null($transaction)) {
            throw new \Exception('Failed to persist transaction.');
        }

        $this->forgeCommand($cmd, $transaction);

        // validate command
        if (!$handler->validate()) {
            $this->entityManager->rollback();
            throw new \Exception('Invalid command.');
        } elseif (!$handler->doPersist()) {
            $this->entityManager->rollback();
        } else {
            $this->entityManager->commit();
        }

        // run command
        $updatedCmd = $handler->run($this->mellatLink);
        if ($handler->doPersist()) {
            $transaction = $this->updateTransactionFromCmd($updatedCmd);
            $this->entityManager->flush($transaction);
        }

        return $updatedCmd;
    }

    public function isInvoicePurchaced(int $invoiceId, TransactionOriginEnum $origin): bool
    {
        $query = $this->entityManager->createQuery("
                SELECT paymentResponse
                FROM App\Entity\Transaction paymentResponse
                WHERE paymentResponse.command = :paymentResponseCommand
                AND paymentResponse.status = :paymentResponseStatus
                AND paymentResponse.parentId IN (
                    SELECT payment.id
                    FROM App\Entity\Transaction payment
                    WHERE payment.command = :paymentCommand
                    AND payment.status = :paymentStatus
                    AND payment.invoiceId = :invoceId
                    AND payment.origin = :origin
                )
            ")
            ->setParameter('paymentResponseCommand', TransactionCmdEnum::PaymentResponse)
            ->setParameter('paymentResponseStatus', TransactionStatusEnum::Success)
            ->setParameter('paymentCommand', TransactionCmdEnum::Payment)
            ->setParameter('paymentStatus', TransactionStatusEnum::Success)
            ->setParameter('invoceId', $invoiceId)
            ->setParameter('origin', $origin);
        
        $transactions = $query->getResult();

        return count($transactions) !== 0;
    }

    public function getPaymentResponseFromPayment(PaymentCmd $cmd): ?PaymentResponseCmd
    {
        $query = $this->entityManager->createQuery("
            SELECT t
            FROM  App\Entity\Transaction t
            WHERE t.command = :command
            AND t.bankToken = :bankToken
            AND t.parentId = :paymentId
            AND t.status = :success
        ")
            ->setParameter('command', TransactionCmdEnum::PaymentResponse)
            ->setParameter('bankToken', $cmd->getBankToken())
            ->setParameter('paymentId', $cmd->getTransactionId())
            ->setParameter('success', TransactionStatusEnum::Success);

        $transaction = $query->getOneOrNullResult();

        return is_null($transaction) ? null : $this->createCmdFromTransaction($transaction);
    }

    public function getInvoicePaymentHistory(int $invoiceId): ?PaymentCmd
    {
        $query = $this->entityManager->createQuery("
            SELECT t
            FROM  App\Entity\Transaction t
            WHERE t.command = :command
            AND t.status = :success
            AND t.invoiceId = :invoiceId
        ")
            ->setParameter('command', TransactionCmdEnum::Payment)
            ->setParameter('success', TransactionStatusEnum::Success)
            ->setParameter('invoiceId', $invoiceId);

        $transaction = $query->getOneOrNullResult();

        return is_null($transaction) ? null : $this->createCmdFromTransaction($transaction);
    }

    public function getCallbackUrl(string $callbackToken)
    {
        $url = $this->router->generate(
            name: 'app.payment.checkout_callback',
            parameters: [
                'callback_token' => $callbackToken
            ],
            referenceType: $this->router::ABSOLUTE_URL,
        );

        return $url;
    }

    static function generatePaymentResponseCmd(string $rawResponse): PaymentResponseCmd
    {
        $callbackResponse = MellatCallback::make($rawResponse);
        return PaymentResponseCmd::make($callbackResponse);
    }

    public function getPaymentFromPaymentResponse(PaymentResponseCmd $cmd): ?PaymentCmd
    {
        $paymentTransaction = $this->transactionRepository->findOneBy([
            'command' => TransactionCmdEnum::Payment,
            'status' => TransactionStatusEnum::Success,
            'id' => $cmd->getPaymentTransactionId(),
            'bankToken' => $cmd->getBankToken(),
        ]);

        return !is_null($paymentTransaction)
            ? $this->createCmdFromTransaction($paymentTransaction)
            : null;
    }

    public function createCmdFromTransaction(Transaction $transaction): BankCmd
    {
        $cmdClassName = $transaction->getCommand()->name . 'Cmd';
        $cmdClassPath = explode('\\', BankCmd::class);
        $cmdClassPath[count($cmdClassPath) - 1] = $cmdClassName;
        $cmdClassFullName = implode('\\', $cmdClassPath);

        $constructor = new ReflectionMethod($cmdClassFullName, '__construct');
        $constructParams = $constructor->getParameters();
        $constructValues = [];

        foreach ($constructParams as $constructParam) {
            $name = $constructParam->getName();
            $transactionProperty = property_exists(Transaction::class, $name)
                ? $name
                : array_search($name, self::TransactionToCmdMapping);

            $transactionGetMethod = 'get' . ucfirst($transactionProperty);
            if (method_exists(Transaction::class, $transactionGetMethod)) {
                if (!is_null($transaction->$transactionGetMethod()))
                    $constructValues[$name] = $transaction->$transactionGetMethod();
            }
        }

        $cmd = new $cmdClassFullName(...$constructValues);

        return $cmd;
    }

    private function initTransactionFromCmd(BankCmd $cmd): Transaction
    {
        $payload = $cmd->getInitList();

        $transaction = new Transaction();
        $transaction
            ->setCommand($cmd->getCommand())
            ->setStatus(TransactionStatusEnum::Pending);

        foreach ($payload as $cmdKey => $value) {
            $transactionKey = property_exists(Transaction::class, $cmdKey)
                ? $cmdKey
                : array_search($cmdKey, self::TransactionToCmdMapping);

            $methodName = 'set' . ucfirst($transactionKey);
            $transaction = $transaction->$methodName($value);
        }

        return $transaction;
    }

    public function updateTransactionFromCmd(BankCmd $cmd)
    {
        $transaction = $this
            ->transactionRepository
            ->find($cmd->getTransactionId());

        $responseList = $cmd->getResponseList();
        foreach ($responseList as $cmdProp => $value) {
            $transactionProp = property_exists(Transaction::class, $cmdProp)
                ? $cmdProp
                : array_search($cmdProp, $this::TransactionToCmdMapping);

            $cmdGetMethod = 'get' . ucfirst($cmdProp);
            $transactionSetMethod = 'set' . ucfirst($transactionProp);
            if (method_exists(Transaction::class, $transactionSetMethod)) {
                $transaction->$transactionSetMethod($cmd->$cmdGetMethod());
            }
        }

        return $transaction;
    }

    public function forgeCommand(BankCmd $cmd, Transaction $transaction): void
    {
        // forge post persist values
        foreach ($cmd->getForgeList() as $cmdProp) {
            $transactionProp = property_exists(Transaction::class, $cmdProp)
                ? $cmdProp
                : array_search($cmdProp, $this::TransactionToCmdMapping);

            $transactionGetMethod = 'get' . ucfirst($transactionProp);
            $forgeCmd = 'forge' . ucfirst($cmdProp);

            $cmd->$forgeCmd($transaction->$transactionGetMethod());
        }
    }

    public function getPaymentResponseVerification(PaymentResponseCmd $cmd): ?VerifyCmd
    {
        return $this->getPaymentResponseFollowUp($cmd, TransactionCmdEnum::Verify);
    }

    public function getPaymentResponseReversal(PaymentResponseCmd $cmd): ?ReversalCmd
    {
        return $this->getPaymentResponseFollowUp($cmd, TransactionCmdEnum::Reversal);
    }

    public function getPaymentResponseSettle(PaymentResponseCmd $cmd): ?SettleCmd
    {
        return $this->getPaymentResponseFollowUp($cmd, TransactionCmdEnum::Settle);
    }

    public function getPaymentResponseFollowUp(
        PaymentResponseCmd $cmd,
        TransactionCmdEnum $type,
    ): ?BankCmd {
        $verifyTransaction = $this->transactionRepository->findOneBy([
            'command' => $type,
            'parentId' => $cmd->getPaymentTransactionId(),
            'bankReferenceId' => $cmd->getBankReferenceId(),
        ]);

        return is_null($verifyTransaction)
            ? null
            : $this->createCmdFromTransaction($verifyTransaction);
    }

    public function getPaymentCmdByTransactionId(int $transactionId): ?PaymentCmd
    {
        $transaction = $this->transactionRepository->find($transactionId);

        return is_null($transaction)
            ? null
            : $this->createCmdFromTransaction($transaction);
    }

    public function generateRedirectLink(PaymentCmd $cmd): ?string
    {
        if ($cmd->getLink() == 'Mellat') {
            return $this->mellatLink->generateRedirectLink($cmd->getBankToken());
        }
        return null;
    }

    public function getCmdStatus(BankCmd $cmd): TransactionStatusEnum
    {
        $link = $cmd->getLink();
        if ($link === 'Mellat') {
            return MellatResponse::calcStatus($cmd->getBankStatus())
                ? TransactionStatusEnum::Success
                : TransactionStatusEnum::Failure;
        }
    }

    public function loadTransactionFromCmd(PaymentResponseCmd $cmd): Transaction
    {
        $query = $this->entityManager->createQuery("
            SELECT t
            FROM App\Entity\Transacion t
            WHERE t.id = :paymentTransactionId
                AND t.bankToken = :bankToken
                AND t.command = :bankCommand
        ")
            ->setParameter('paymentTransactionId', $cmd->getTransactionId())
            ->setParameter('bankToken', $cmd->getBankToken())
            ->setParameter('command', $cmd->getCommand());

        $transaction = $query->getResult();

        return $transaction;
    }
}
