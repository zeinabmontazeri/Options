<?php

namespace App\Payment\Note;

use DateTimeImmutable;

trait CmdForgeTrait
{
    public function forgeTransactionId(int $transactionId)
    {
        try {
            $this->transactionId = $transactionId;
        } catch (\Exception $e) {
            throw new \Exception('Transaction Id already forged.');
        }
    }

    public function forgeCreatedAt(DateTimeImmutable $createdAt)
    {
        try {
            $this->createdAt = $createdAt;
        } catch (\Exception $e) {
            throw new \Exception('CreatedAt already forged.');
        }
    }
}
