<?php

namespace App\TaskDefinition;

interface PaymentTaskInterface
{
    /*
     * The unique id of request
     */
    public readonly int $orderId;

    /*
     * Reference to the pointed sale
     */
    public readonly ?int $saleOrderId;

    /*
     * The reference code returned from bank for sale
     */
    public readonly ?int $saleReferenceId;

    /*
     * Data payload for transactions
     */
    public readonly ?array $payload;
}
