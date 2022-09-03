<?php

namespace App\Payment\Cmd;

use App\Entity\TransactionOriginEnum;
use App\Entity\TransactionStatusEnum;

class PaymentCmd extends BankCmd
{
    use CmdForgeTrait;

    protected readonly int $transactionId;
    protected readonly \DateTimeImmutable $createdAt;
    protected readonly string $link;
    protected readonly TransactionStatusEnum $status;
    protected readonly int $bankStatus;
    private readonly string $callbackToken;
    private readonly string $bankToken;

    public function __construct(
        protected readonly int $invoiceId,
        protected readonly int $userId,
        protected readonly TransactionOriginEnum $origin,
        protected readonly string $amount,
        protected readonly string $note,
        ?int $transactionId = null,
        ?\DateTimeImmutable $createdAt = null,
        ?string $callbackToken = null,
        ?TransactionStatusEnum $status = null,
        ?int $bankStatus = null,
        ?string $bankToken = null,
        ?string $link = null,
    ) {
        if (!is_null($transactionId)) {
            $this->transactionId = $transactionId;
        }

        if (!is_null($createdAt)) {
            $this->createdAt = $createdAt;
        }

        if (!is_null($callbackToken)) {
            $this->callbackToken = $callbackToken;
        }

        if (!is_null($status)) {
            $this->status = $status;
        }

        if (!is_null($bankStatus)) {
            $this->bankStatus = $bankStatus;
        }

        if (!is_null($bankToken)) {
            $this->bankToken = $bankToken;
        }

        if (!is_null($link)) {
            $this->link = $link;
        }
    }

    public function getOrigin(): TransactionOriginEnum
    {
        return $this->origin;
    }

    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function forgeCallbackToken(string $callbackToken): self
    {
        try {
            $this->callbackToken = $callbackToken;
        } catch (\Exception $e) {
            throw new \Exception('Callback token already forged.');
        }

        return $this;
    }

    public function getCallbackToken(): ?string
    {
        return isset($this->callbackToken) ? $this->callbackToken : null;
    }

    public function forgeBankToken(string $bankToken): self
    {
        try {
            $this->bankToken = $bankToken;
        } catch (\Exception $e) {
            throw new \Exception('Bank token already forged.');
        }

        return $this;
    }

    public function getBankToken(): ?string
    {
        return isset($this->bankToken) ? $this->bankToken : null;
    }

    public function getInitList(): array
    {
        return [
            'origin' => $this->getOrigin(),
            'invoiceId' => $this->getInvoiceId(),
            'userId' => $this->getUserId(),
            'amount' => $this->getAmount(),
            'note' => $this->getNote(),
            'link' => $this->getLink(),
        ];
    }

    public static function getForgeList(): array
    {
        return array_merge(
            parent::getForgeList(),
            ['callbackToken']
        );
    }

    public function getResponseList(): array
    {
        return [
            'status' => $this->getStatus(),
            'bankStatus' => $this->getBankStatus(),
            'bankToken' => $this->getBankToken(),
        ];
    }
}
