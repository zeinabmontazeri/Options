<?php

namespace App\Request;

use App\Entity\Experience;
use Doctrine\DBAL\Types\Types;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints as Assert;

class EventRequest extends BaseRequest
{
    use ValidateRequestTrait;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\NotNull]
    public ?int $capacity = 0;

    #[Assert\NotNull]
    public ?int $duration = null;

    #[Assert\NotNull]
    public ?string $price = null;

    #[Assert\Type(type: 'boolean')]
    #[Assert\NotNull]
    public ?bool $isOnline = null;

    #[Assert\GreaterThan(new \DateTime())]
    #[Assert\NotNull]
    public ?\DateTimeInterface $startsAt = null;

    #[Assert\Expression("this.link or !this.isOnline", message: "if event is online link can not be blank.")]
    public ?string $link = null;

    #[Assert\Expression("this.address or this.isOnline", message: "if event is not online address can not be blank.")]
    public ?string $address = null;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}