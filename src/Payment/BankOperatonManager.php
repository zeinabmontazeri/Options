<?php

namespace App\Payment;

use App\Auth\AuthenticatedUser;
use App\Entity\Transaction;
use App\Entity\TransactionOriginEnum;
use App\Entity\TransactionStatusEnum;
use App\Payment\Bank\Mellat\Link as MellatLink;
use App\Payment\Bank\Mellat\CallbackResponse as MellatCallback;
use App\Payment\Bank\Mellat\Response as MellatResponse;
use App\Payment\Cmd\BankCmd;
use App\Payment\Cmd\PaymentCmd;
use App\Payment\Cmd\PaymentResponseCmd;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionMethod;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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

        $this->forgeCommand($cmd, $transaction);

        // validate command
        if (!$handler->validate()) {
            $this->entityManager->rollback();
            throw new BadRequestHttpException('Payment failed.');
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
        return $this->transactionRepository->isInvoicePurchaced($invoiceId, $origin);
    }

    public function getCallbackUrl(string $callbackToken)
    {
        $url = $this->router->generate(
            name: 'app_payment_checkout_callback',
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
        $paymentTransaction = $this
            ->transactionRepository
            ->getPayment(
                status: TransactionStatusEnum::Success,
                id: $cmd->getPaymentTransactionId(),
                bankToken: $cmd->getBankToken(),
            );

        return !is_null($paymentTransaction)
            ? $this->createCmdFromTransaction($paymentTransaction)
            : null;
    }

    private function createCmdFromTransaction(Transaction $transaction): BankCmd
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

    private function updateTransactionFromCmd(BankCmd $cmd)
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
}
