<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\EnumEventStatus;

class ExperienceStatusUpdateRequest extends BaseRequest
{

    use ValidateRequestTrait;


    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(['DRAFT', 'CANCEL','PUBLISHED'])]
    public readonly string $status;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}