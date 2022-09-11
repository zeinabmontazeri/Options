<?php

namespace App\Request;

class ExperienceSearchRequest extends BaseRequest
{
    use ValidateRequestTrait;

    public readonly ?string $word;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}