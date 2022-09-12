<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ExperienceUpdateRequest extends BaseRequest
{
    use ValidateRequestTrait;

    #[Assert\Length(min: 20)]
    public readonly ?string $description;

    public readonly ?string $title;

    public readonly ?string $categoryName;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}