<?php

namespace App\Request;

class HostAuthorizationFilterRequest extends BaseRequest
{
    use ValidateRequestTrait;

    public ?bool $accepted = null;
    public ?bool $rejected = null;
    public ?bool $pending = null;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}
