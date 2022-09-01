<?php

namespace App\Payment\Note;

use App\Entity\TransactionOriginEnum;
use DateTimeImmutable;

class PaymentCmd extends BankCmd
{
    use CmdForgeTrait;

    protected readonly int $transactionId;
    protected readonly DateTimeImmutable $createdAt;
    private readonly string $callBackToken;

    public function __construct(
        protected readonly int $invoiceId,
        protected readonly int $userId,
        protected readonly string $requestedRole,
        protected readonly TransactionOriginEnum $origin,
        protected readonly string $amount,
        protected readonly string $note,
    ) {
    }

    public function getRequestedRole(): string
    {
        return $this->requestedRole;
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

    public function getCallbackToken(): string
    {
        if (isset($this->callBackToken)) {
            return $this->callBackToken;
        }

        if (!isset($this->createdAt) or !isset($this->transactionId)) {
            throw new \Exception('Cannot create callback token before persisting request');
        }

        $public = strval($this->getCreatedAt()->getTimestamp()) . strval($this->getAmount());
        $private = substr(md5('PaymentRequest$' . $public . '$'), 10);
        $token = base64_encode($private . '.' . $public);

        $this->callBackToken = $token;

        return $this->callBackToken;
    }

    public function getPayload(): array
    {
        return [
            'invoiceId' => $this->getInvoiceId(),
            'userId' => $this->getUserId(),
            'amount' => $this->getAmount(),
            'note' => $this->getNote(),
            'callbackToken' => $this->getCallbackToken(),
        ];
    }
}
