<?php

namespace App\Payment\Cmd;

use App\Entity\TransactionCmdEnum;
use App\Entity\TransactionStatusEnum;

abstract class BankCmd implements BankCmd
{
    abstract public function forgeTransactionId(int $transactionId): self;
    abstract public function forgeCreatedAt(\DateTimeImmutable $createdAt): self;
    abstract public function forgeLink(string $link): self;
    abstract public function getInitList(): array;
    abstract public function getResponseList(): array;

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
        return isset($this->transactionId) ? $this->transactionId : null;
    }

    public function getCommand(): TransactionCmdEnum
    {
        $classPath = explode("\\", static::class);
        $cmdClassName = end($classPath);
        $cmdName = substr($cmdClassName, 0, strlen($cmdClassName) - strlen('Cmd'));

        $cmdParts = [];
        preg_match_all('/[A-Z]+[a-z]*/', $cmdName, $cmdParts);
        $cmdParts = array_map('strtoupper', $cmdParts[0]);

        return TransactionCmdEnum::from('CMD_' . implode('_', $cmdParts));
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return isset($this->createdAt) ? $this->createdAt : null;
    }

    public function getLink(): ?string
    {
        return isset($this->link) ? $this->link : null;
    }

    public function getStatus(): ?TransactionStatusEnum
    {
        return isset($this->status) ? $this->status : null;
    }

    public function getBankStatus(): ?int
    {
        return isset($this->bankStatus) ? $this->bankStatus : null;
    }

    public static function getForgeList(): array
    {
        return [
            'transactionId',
            'createdAt',
        ];
    }
}
