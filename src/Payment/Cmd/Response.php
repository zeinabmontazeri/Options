<?php

namespace App\Payment\Cmd;

use App\Entity\Enums\TransactionStatusEnum;
use App\Payment\Bank\ResponseInterface;

class Response
{
    public function __construct(
        private readonly TransactionStatusEnum $status,
        private readonly ?string $bankToken,
        private readonly int $bankStatus,
        private readonly ?string $redirectLink,
    ) {
    }

    static function make(ResponseInterface $linkResponse)
    {
        return new static(
            bankToken: $linkResponse->getBankToken(),
            bankStatus: $linkResponse->getBankStatus(),
            status: $linkResponse->getStatus()
                ? TransactionStatusEnum::Success
                : TransactionStatusEnum::Failure,
            redirectLink: $linkResponse->getRedirectLink(),
        );
    }

    public function getStatus(): TransactionStatusEnum
    {
        return $this->status;
    }

    public function getBankStatus(): int
    {
        return $this->bankStatus;
    }

    public function getBankToken(): ?string
    {
        return $this->bankToken;
    }

    public function getRedirectLink(): ?string
    {
        return $this->redirectLink;
    }
}
