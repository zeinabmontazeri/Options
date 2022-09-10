<?php

namespace App\Payment\Cmd;

use App\Entity\TransactionStatusEnum;
use App\Payment\Bank\CallbackResponseInterface;

class PaymentResponseCmd extends BankCmd
{
    use CmdForgeTrait;

    protected readonly int $transactionId;
    protected readonly \DateTimeImmutable $createdAt;
    protected readonly string $link;
    protected readonly TransactionStatusEnum $status;

    public function __construct(
        protected readonly int $bankStatus,
        private readonly string $bankToken,
        private readonly int $paymentTransactionId,
        private readonly int $bankReferenceId,
        private readonly string $cardInfo,
        ?int $transactionId = null,
        ?\DateTimeImmutable $createdAt = null,
        ?TransactionStatusEnum $status = null,
        ?string $link = null,
    ) {
        if (!is_null($transactionId)) {
            $this->transactionId = $transactionId;
        }

        if (!is_null($createdAt)) {
            $this->createdAt = $createdAt;
        }

        if (!is_null($status)) {
            $this->status = $status;
        }

        if (!is_null($link)) {
            $this->link = $link;
        }
    }

    public function getBankToken(): string
    {
        return $this->bankToken;
    }

    public function getPaymentTransactionId(): int
    {
        return $this->paymentTransactionId;
    }

    public function getBankReferenceId(): int
    {
        return $this->bankReferenceId;
    }

    public function getCardInfo(): string
    {
        return $this->cardInfo;
    }

    static function make(CallbackResponseInterface $response)
    {
        return new static(
            bankToken: $response->getBankToken(),
            bankStatus: $response->getBankStatus(),
            paymentTransactionId: $response->getPaymentTransactionId(),
            bankReferenceId: $response->getBankReferenceId(),
            cardInfo: $response->getCardInfo(),
        );
    }

    public function getResponseList(): array
    {
        return [
            'status' => $this->getStatus(),
        ];
    }
    
    public function getInitList(): array
    {
        return [
            'bankToken' => $this->getBankToken(),
            'bankStatus' => $this->getBankStatus(),
            'paymentTransactionId' => $this->getPaymentTransactionId(),
            'bankReferenceId' => $this->getBankReferenceId(),
            'cardInfo' => $this->getCardInfo(),
            'link' => $this->getLink(),
        ];
    }
}
