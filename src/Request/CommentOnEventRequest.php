<?php

namespace App\Request;
use Symfony\Component\Validator\Constraints as Assert;
class CommentOnEventRequest extends BaseRequest
{
    use ValidateRequestTrait;
    #[Assert\NotNull]
    #[Assert\Positive]
    public readonly ?int $userId;

    #[Assert\NotNull]
    #[Assert\Positive]
    public readonly ?int $eventId;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public readonly ?string $description;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}