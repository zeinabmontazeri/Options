<?php

namespace App\Payment\Bank\Mellat;

use App\Payment\Bank\ResponseInterface;

class Response implements ResponseInterface
{
    private readonly int $bankStatus;
    private readonly string $token;

    public function __construct(
        private readonly string $rawResponse
    ) {
        $this->processRawResponse($rawResponse);
    }

    private function processRawResponse(string $rawResponse)
    {
        if (!strpos($rawResponse, ',')) {
            $this->bankStatus = intval($rawResponse);
        } else {
            $parts = explode(',', $rawResponse);
            $this->bankStatus = intval($parts[0]);
            $this->token = $parts[1];
        }
    }

    public function getStatus(): bool
    {
        return $this->getBankStatus() === 0;
    }

    public function getBankToken(): ?string
    {
        return isset($this->token) ? $this->token : null;
    }

    public function getBankStatus(): int
    {
        return $this->bankStatus;
    }

    public function getBankStatusMessage(): ?string
    {
        return ResponseEnum::tryFrom($this->getBankStatus())->name;
    }

    public function getRedirectLink(): ?string
    {
        return Link::generateRedirectLink($this->getBankToken());
    }

    public static function calcStatus(int $bankStatus): bool
    {
        return $bankStatus === 0;
    }
}
