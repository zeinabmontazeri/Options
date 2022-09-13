<?php

namespace App\Payment\Event;

use App\Entity\Enums\TransactionOriginEnum;
use Symfony\Contracts\EventDispatcher\Event;

class PurchaseFailEvent extends Event
{
    public function __construct(
        private readonly TransactionOriginEnum $origin,
        private readonly int $invoiceId,
    ) {
    }

    public function getOrigin(): TransactionOriginEnum
    {
        return $this->origin;
    }

    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }
}
