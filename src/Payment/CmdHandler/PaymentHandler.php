<?php

namespace App\Payment\CmdHandler;

use App\Entity\Enums\TransactionStatusEnum;
use App\Payment\Bank\LinkInterface;
use App\Payment\BankOperatonManager;
use App\Payment\Cmd\BankCmd;
use App\Payment\Cmd\PaymentCmd;

class PaymentHandler implements CmdHandlerInterface
{
    public function __construct(
        private PaymentCmd $cmd,
        private BankOperatonManager $operationManager,
    ) {
    }

    public function doPersist(): bool
    {
        return true;
    }

    public function validate(): bool
    {
        $isPurchased = $this->operationManager->isInvoicePurchaced(
            invoiceId: $this->cmd->getInvoiceId(),
            origin: $this->cmd->getOrigin()
        );

        return !$isPurchased;
    }

    public function run(LinkInterface $link): ?BankCmd
    {
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
    }
}
