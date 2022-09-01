<?php

namespace App\Payment\Note;

class VerifyCmd extends BankCmd
{
    use CmdForgeTrait;

    protected readonly int $transactionId;
    protected readonly \DateTimeImmutable $createdAt;

    public function __construct(
        private readonly int $paymentTransactionId,
        private readonly string $bankToken,
    )
    {
    }

    public function getPaymentTransactionId()
    {
        return $this->paymentTransactionId;
    }

    public function getBankToken()
    {
        return $this->bankToken;
    }
}