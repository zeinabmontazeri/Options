<?php

namespace App\Request;

use App\Entity\Enums\EnumPermissionStatus;
use Symfony\Component\Validator\Constraints as Assert;


class AuthorizeAdminRequest extends BaseRequest
{
    use ValidateRequestTrait;

    #[Assert\Choice(choices: [EnumPermissionStatus::ACCEPTED, EnumPermissionStatus::REJECTED, EnumPermissionStatus::PENDING])]
    public readonly ?EnumPermissionStatus $approvalStatus;

    protected function autoValidateRequest(): bool
    {
        return true;
    }

}