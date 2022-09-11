<?php

namespace App\Payment\Cmd;

use App\Entity\TransactionStatusEnum;

class ReversalCmd extends BankCmd
{
    use CmdForgeTrait;

    protected readonly int $transactionId;
    protected readonly \DateTimeImmutable $createdAt;
    protected readonly string $link;
    protected TransactionStatusEnum $status;
    protected ?int $bankStatus;

    public function __construct(
        private readonly int $paymentTransactionId,
        private readonly int $bankReferenceId,
        ?TransactionStatusEnum $status=null,
        ?int $bankStatus=null,
        ?string $link=null,
    ) {
        if(!is_null($status))
        {
            $this->status = $status;
        }

        if(!is_null($bankStatus))
        {
            $this->bankStatus = $bankStatus;
        }

        if(!is_null($link))
        {
            $this->link = $link;
        }
    }

    public function getPaymentTransactionId(): int
    {
        return $this->paymentTransactionId;
    }

    public function getBankReferenceId(): int
    {
        return $this->bankReferenceId;
    }

    public function getResponseList(): array
    {
        return [
            'status' => $this->getStatus(),
            'bankStatus' => $this->getBankStatus(),
        ];
    }
    
    public function getInitList(): array
    {
        return [
            'paymentTransactionId' => $this->getPaymentTransactionId(),
            'bankReferenceId' => $this->getBankReferenceId(),
            'link' => $this->getLink(),
        ];
    }
}
