<?php

namespace App\Payment\CmdHandler;

use App\Entity\TransactionStatusEnum;
use App\Payment\Bank\LinkInterface;
use App\Payment\BankOperatonManager;
use App\Payment\Cmd\BankCmd;
use App\Payment\Cmd\PaymentResponseCmd;
use App\Payment\Cmd\ReversalCmd;
use App\Payment\Cmd\SettleCmd;
use App\Payment\Cmd\VerifyCmd;

class PaymentResponseHandler implements CmdHandlerInterface
{
    public function __construct(
        private PaymentResponseCmd $cmd,
        private BankOperatonManager $operationManager,
    ) {
    }

    public function validate(): bool
    {
        // check if the claimed payment match payment transaction
        //  history in the data provider
        $paymentCmd = $this
            ->operationManager
            ->getPaymentFromPaymentResponse($this->cmd);

        if (is_null($paymentCmd)) {
            return false;
        }

        $invoicePurchased = $this
            ->operationManager
            ->isInvoicePurchaced($paymentCmd->getInvoiceId());

        if ($invoicePurchased) {
            return false;
        }

        return true;
    }

    public function run(LinkInterface $link): ?BankCmd
    {
        // check if it claims failure payment
        if ($this->operationManager->getCmdStatus($this->cmd) === TransactionStatusEnum::Failure) {
            // TODO: dispatch payment result 
            // genereate failure response with redirect to item page
            $this->cmd->forgeStatus(TransactionStatusEnum::Failure);
            return $this->cmd;
        }

        // if it claims success payment
        // check if it is verified
        $verifyCmd = $this->callVerify($this->cmd);

        if ($verifyCmd->getStatus() === TransactionStatusEnum::Failure) {
            $reversalCmd = $this->callReversal($this->cmd);
            $this->cmd->forgeStatus(TransactionStatusEnum::Failure);
            return $this->cmd;
        } elseif ($verifyCmd->getStatus() === TransactionStatusEnum::Success) {
            $settleCmd = $this->callSettle($this->cmd);
            if ($settleCmd->getStatus() === TransactionStatusEnum::Success) {
                $this->cmd->forgeStatus(TransactionStatusEnum::Success);
                return $this->cmd;
            } elseif ($settleCmd->getStatus() === TransactionStatusEnum::Failure) {
                $reversalCmd = $this->callReversal($this->cmd);
                $this->cmd->forgeStatus(TransactionStatusEnum::Failure);
                return $this->cmd;
            }
        }

        $this->cmd->forgeStatus(TransactionStatusEnum::Failure);
        return $this->cmd;
    }

    public function doPersist(): bool
    {
        return true;
    }

    private function callVerify(PaymentResponseCmd $cmd): VerifyCmd
    {
        // verify request
        $verifyCmd = $this->operationManager->getPaymentResponseVerification($cmd);
        if (is_null($verifyCmd)) {
            $verifyCmd = new VerifyCmd(
                paymentTransactionId: $cmd->getPaymentTransactionId(),
                bankReferenceId: $cmd->getBankReferenceId(),
            );

            $verifyCmd = $this->operationManager->run($verifyCmd);
        }

        return $verifyCmd;
    }

    private function callSettle(PaymentResponseCmd $cmd): SettleCmd
    {
        // settle request
        $settleCmd = $this->operationManager->getPaymentResponseSettle($cmd);
        if (is_null($settleCmd)) {
            $settleCmd = new SettleCmd(
                paymentTransactionId: $cmd->getPaymentTransactionId(),
                bankReferenceId: $cmd->getBankReferenceId(),
            );

            $settleCmd = $this->operationManager->run($settleCmd);
        }

        return $settleCmd;
    }

    private function callReversal(PaymentResponseCmd $cmd): ReversalCmd
    {
        // reversal request
        $reversalCmd = $this->operationManager->getPaymentResponseReversal($cmd);
        if (is_null($reversalCmd)) {
            $reversalCmd = new ReversalCmd(
                paymentTransactionId: $cmd->getPaymentTransactionId(),
                bankReferenceId: $cmd->getBankReferenceId(),
            );

            $reversalCmd = $this->operationManager->run($reversalCmd);
        }

        return $reversalCmd;
    }
}
