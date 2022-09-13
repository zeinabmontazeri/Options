<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class HostApproveRequest extends BaseRequest
{
    use ValidateRequestTrait;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    public ?int $request_id = 0;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(["ACCEPTED", "REJECTED"])]
    public readonly ?string $status;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}
