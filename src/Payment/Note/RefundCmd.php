<?php

namespace App\Payment\Note;

class RefundCmd extends BankCmd
{
    use CmdForgeTrait;

    protected readonly int $transactionId;
    protected readonly \DateTimeImmutable $createdAt;

    public function __construct(
        private readonly int $paymentTransactionId,
        private readonly string $bankToken,
        private readonly string $amount,
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

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getPayload(): array
    {
        return [
            'amount' => $this->getAmount(),
            'parentId' => $this->getPaymentTransactionId(),
            'bankToken' => $this->getBankToken(),
        ];
    }
}
