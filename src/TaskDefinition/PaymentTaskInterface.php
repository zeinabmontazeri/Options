<?php

namespace App\TaskDefinition;

interface PaymentTaskInterface
{
    /*
     * The unique id of request
     */
    public string $orderId;

    /*
     * Reference to the pointed sale
     */
    public ?string $saleOrderId;

    /*
     * The reference code returned from bank for sale
     */
    public ?string $saleReferenceId;

    /*
     * Data payload for transactions
     */
    public ?array $payload;
}