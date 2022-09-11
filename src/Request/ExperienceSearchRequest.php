<?php

namespace App\Request;

class ExperienceSearchRequest extends BaseRequest
{
    use ValidateRequestTrait;

    public ?string $word = null;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}