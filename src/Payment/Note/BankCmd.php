<?php

namespace App\Payment\Note;

use App\Entity\TransactionCmdEnum;

abstract class BankCmd extends BankNote
{
    abstract public function forgeTransactionId(int $transactionId): self;
    abstract public function forgeCreatedAt(\DateTimeImmutable $createdAt): self;
    abstract public function getPayload(): array;

    public function getRunner(): string
    {
        $classPath = explode("\\", static::class);

        $cmdClassName = end($classPath);
        $cmdName = substr($cmdClassName, 0, strlen($cmdClassName) - strlen('Cmd'));
        $handlerName = $cmdName . 'Handler';

        $classPath[count($classPath) - 1] = $handlerName;
        $classPath[count($classPath) - 2] = 'CmdHandler';

        return implode("\\", $classPath);
    }

    public function getTransactionId(): ?int
    {
        return $this->transactionId;
    }

    public function getCommand(): TransactionCmdEnum
    {
        $classPath = explode("\\", static::class);
        $cmdClassName = end($classPath);
        $cmdName = substr($cmdClassName, 0, strlen($cmdClassName) - strlen('Cmd'));

        return TransactionCmdEnum::from('CMD_' . strtoupper($cmdName));
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
