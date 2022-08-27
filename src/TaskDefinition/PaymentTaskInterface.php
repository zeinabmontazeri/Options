<?php

namespace App\TaskDefinition;

interface PaymentTaskInterface
{
    /*
     * The unique id of request
     */
    public readonly string $orderId;

    /*
     * Reference to the pointed sale
     */
    public readonly ?string $saleOrderId;

    /*
     * The reference code returned from bank for sale
     */
    public readonly ?string $saleReferenceId;

    /*
     * Data payload for transactions
     */
    public readonly ?array $payload;
}