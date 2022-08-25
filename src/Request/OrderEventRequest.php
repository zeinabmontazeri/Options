<?php

namespace App\Request;
use Symfony\Component\Validator\Constraints as Assert;

class OrderEventRequest extends BaseRequest
{
    use ValidateRequestTrait;

    #[Assert\NotNull]
    #[Assert\Positive]
    public readonly ?int $userId;

    #[Assert\NotNull]
    #[Assert\Positive]
    public readonly ?int $eventId;
    protected function autoValidateRequest(): bool
    {
        return true;
    }
}