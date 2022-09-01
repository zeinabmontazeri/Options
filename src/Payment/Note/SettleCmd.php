<?php

namespace App\Payment\Note;

class SettleCmd extends BankCmd
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

    public function getPayload(): array
    {
        return [
            'parentId' => $this->getPaymentTransactionId(),
            'bankToken' => $this->getBankToken(),
        ];
    }
}
