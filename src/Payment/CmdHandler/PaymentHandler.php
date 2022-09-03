<?php

namespace App\Payment\CmdHandler;

use App\Entity\TransactionStatusEnum;
use App\Payment\Bank\LinkInterface;
use App\Payment\BankOperatonManager;
use App\Payment\Cmd\BankCmd;
use App\Payment\Cmd\PaymentCmd;

class PaymentHandler implements CmdHandlerInterface
{
    private ?string $decision = null;

    public function __construct(
        private PaymentCmd $cmd,
        private BankOperatonManager $operationManager,
    ) {
    }

    public function doPersist(): bool
    {
        return $this->decision === 'new';
    }

    public function validate(): bool
    {
        $paymentCmd = $this
            ->operationManager
            ->getInvoicePaymentHistory($this->cmd->getInvoiceId());

        // Payment Transaction:
        // if there is not any successfull transaction it is a valid request
        if (is_null($paymentCmd)) {
            $this->decision = 'new';
            return true;
        }

        // load its payment response
        $paymentResponseCmd = $this
            ->operationManager
            ->getPaymentResponseFromPayment($paymentCmd);

        if (is_null($paymentResponseCmd)) {
            $this->decision = $paymentCmd->getTransactionId();
            return true;
        }

        return !is_null($this->decision);
    }

    public function run(LinkInterface $link): ?BankCmd
    {
        if ($this->decision === 'new') {
            $response = $link->payment(
                transactionId: $this->cmd->getTransactionId(),
                userId: $this->cmd->getUserId(),
                createdAt: $this->cmd->getCreatedAt(),
                note: $this->cmd->getNote(),
                amount: (float) $this->cmd->getAmount(),
                callbackUrl: $this->operationManager->getCallbackUrl($this->cmd->getCallbackToken()),
            );

            $this->cmd
                ->forgeStatus(
                    $response->getStatus()
                        ? TransactionStatusEnum::Success
                        : TransactionStatusEnum::Failure
                )
                ->forgeBankStatus($response->getBankStatus())
                ->forgeBankToken($response->getBankToken());

            return $this->cmd;
        } else {
            return $this->operationManager->getPaymentCmdByTransactionId(intval($this->decision));
        }
    }
}
