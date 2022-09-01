<?php

namespace App\Payment\Note;

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
}
