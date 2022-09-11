<?php

namespace App\Payment\CmdHandler;

use App\Entity\TransactionStatusEnum;
use App\Payment\Bank\LinkInterface;
use App\Payment\BankOperatonManager;
use App\Payment\Cmd\BankCmd;
use App\Payment\Cmd\ReversalCmd;

class ReversalHandler implements CmdHandlerInterface
{
    public function __construct(
        private ReversalCmd $cmd,
        private BankOperatonManager $operationManager,
    ) {
    }

    public function validate(): bool
    {
        return true;
    }

    public function doPersist(): bool
    {
        return true;
    }

    public function run(LinkInterface $link): ?BankCmd
    {
        $response = $link->reversal(
            transactionId: $this->cmd->getTransactionId(),
            paymentTransactionId: $this->cmd->getPaymentTransactionId(),
            bankReferenceId: $this->cmd->getBankReferenceId(),
        );

        $this->cmd
            ->forgeBankStatus($response->getBankStatus())
            ->forgeStatus(
                $response->getStatus()
                    ? TransactionStatusEnum::Success
                    : TransactionStatusEnum::Failure
            );

        return $this->cmd;
    }
}
