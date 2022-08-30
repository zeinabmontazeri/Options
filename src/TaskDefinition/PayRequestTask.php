<?php

namespace App\TaskDefinition;

class PayRequestTask implements PaymentTaskInterface
{
    public function __construct(
        public readonly int $orderId,
        int $amount,
        string $additionalData,
        int $payerId,
    )
    {
        $dateTime = new \DateTimeImmutable('now', new \DateTimeZone('Asia/Tehran'));
        $this->payload = [
            'amount' => $amount,
            'localDate' => $dateTime->format('Y-m-d'),
            'localTime' => $dateTime->format('H:i:s'),
            'additionalData' => $additionalData,
            'payerId' => $payerId
        ];
    }
}
