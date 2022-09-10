<?php

namespace App\Payment\Bank;

interface CallbackResponseInterface
{
    public function getBankToken(): string;

    public function getBankStatus(): int;

    public function getPaymentTransactionId(): int;

    public function getBankReferenceId(): int;

    public function getCardInfo(): string;

    public function getStatus(): bool;
}
