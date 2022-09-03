<?php

namespace App\Payment\Cmd;

use App\Entity\TransactionStatusEnum;
use DateTimeImmutable;

trait CmdForgeTrait
{
    public function forgeTransactionId(int $transactionId): self
    {
        try {
            $this->transactionId = $transactionId;
        } catch (\Exception $e) {
            throw new \Exception('Transaction Id already forged.');
        }

        return $this;
    }

    public function forgeCreatedAt(DateTimeImmutable $createdAt): self
    {
        try {
            $this->createdAt = $createdAt;
        } catch (\Exception $e) {
            throw new \Exception('CreatedAt already forged.');
        }

        return $this;
    }

    public function forgeLink(string $link): self
    {
        try {
            $this->link = $link;
        } catch (\Exception $e) {
            throw new \Exception('CreatedAt already forged.');
        }

        return $this;
    }

    public function forgeStatus(TransactionStatusEnum $status): self
    {
        try {
            $this->status = $status;
        } catch (\Exception $e) {
            throw new \Exception('Status already forged.');
        }

        return $this;
    }

    public function forgeBankStatus(int $bankStatus): self
    {
        try {
            $this->bankStatus = $bankStatus;
        } catch (\Exception $e) {
            throw new \Exception('Bank status already forged.');
        }

        return $this;
    }
}
