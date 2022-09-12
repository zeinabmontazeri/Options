<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class EventUpdateRequest extends BaseRequest
{
    use ValidateRequestTrait;

    #[Assert\GreaterThanOrEqual(1)]
    public ?int $capacity = null;

    public ?int $duration = null;

    #[Assert\Regex(
        pattern: '/[1-9]{1}[0-9]{3,}/',
        message: 'Price must be an integer greater than 1000.',
    )]
    public ?string $price = null;

    #[Assert\Type(type: 'boolean')]
    public ?bool $isOnline = null;

    #[Assert\GreaterThan(new \DateTime())]
    public ?\DateTimeInterface $startsAt = null;

    public ?string $link = null;

    public ?string $address = null;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}