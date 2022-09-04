<?php

namespace App\Payment\Bank\Mellat;

use App\Payment\Bank\CallbackResponseInterface;

class CallbackResponse implements CallbackResponseInterface
{
    public function __construct(
        private readonly string $bankToken,
        private readonly int $bankStatus,
        private readonly int $paymentTransactionId,
        private readonly int $bankReferenceId,
        private readonly string $cardInfo,
    ) {
    }

    static function make(string $rawResponse): static
    {
        $params = static::parseRawResponse($rawResponse);

        return new static(
            bankToken: $params['RefId'],
            bankStatus: intval($params['ResCode']),
            paymentTransactionId: intval($params['SaleOrderId']),
            bankReferenceId: intval($params['SaleReferenceId']),
            cardInfo: $params['CardHolderInfo'],
        );
    }

    private static function parseRawResponse(string $rawResponse): array
    {
        $parts = explode('&', $rawResponse);
        $params = array_reduce($parts, function ($acc, $item) {
            $item_parts = explode('=', $item);
            $acc[$item_parts[0]] = $item_parts[1];
            return $acc;
        }, []);

        return $params;
    }

    public function getBankToken(): string
    {
        return $this->bankToken;
    }

    public function getBankStatus(): int
    {
        return $this->bankStatus;
    }

    public function getPaymentTransactionId(): int
    {
        return $this->paymentTransactionId;
    }

    public function getBankReferenceId(): int
    {
        return $this->bankReferenceId;
    }

    public function getCardInfo(): string
    {
        return $this->cardInfo;
    }

    public function getStatus(): bool
    {
        return $this->getBankStatus() === 0;
    } 
}
