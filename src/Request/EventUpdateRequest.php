<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class EventUpdateRequest extends BaseRequest
{
    use ValidateRequestTrait;

    #[Assert\GreaterThanOrEqual(1)]
    public readonly ?int $capacity;

    public readonly ?int $duration;

    #[Assert\Regex(
        pattern: '/[1-9]{1}[0-9]{3,}/',
        message: 'Price must be an integer greater than 1000.',
    )]
    public readonly ?string $price;

    #[Assert\Type(type: 'boolean')]
    public readonly ?bool $isOnline;

    #[Assert\GreaterThan(new \DateTime())]
    public readonly ?\DateTimeInterface $startsAt;

    #[Assert\Expression("this.link or !this.isOnline", message: "if event is online link can not be blank.")]
    public readonly ?string $link;

    #[Assert\Expression("this.address or this.isOnline", message: "if event is not online address can not be blank.")]
    public readonly ?string $address;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}