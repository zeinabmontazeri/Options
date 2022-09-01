<?php

namespace App\Payment\Note;

class InqueryCmd extends BankCmd
{
    use CmdForgeTrait;

    protected readonly int $transactionId;
    protected readonly \DateTimeImmutable $createdAt;

    public function __construct(
        private readonly int $paymentTransactionId,
        private readonly string $bankToken,
    ) {
    }

    public function getPaymentTransactionId(): int
    {
        return $this->paymentTransactionId;
    }

    public function getBankToken(): string
    {
        return $this->bankToken;
    }
}
