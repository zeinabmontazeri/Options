<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ExperienceRequest extends BaseRequest
{
    use ValidateRequestTrait;

    #[Assert\NotBlank]
    #[Assert\Length(min: 20)]
    public readonly ?string $description;

    #[Assert\NotBlank]
    public readonly ?string $title ;

    #[Assert\NotBlank]
    public readonly ?string $category_name ;
    protected function autoValidateRequest(): bool
    {
        return true;
    }
}