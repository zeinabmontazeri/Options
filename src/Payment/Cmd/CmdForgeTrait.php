<?php

namespace App\Payment\Cmd;

use App\Entity\TransactionStatusEnum;
use DateTimeImmutable;

trait CmdForgeTrait
{
    public function forgeTransactionId(int $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function forgeCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function forgeLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function forgeStatus(TransactionStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function forgeBankStatus(int $bankStatus): self
    {
        $this->bankStatus = $bankStatus;

        return $this;
    }
}
