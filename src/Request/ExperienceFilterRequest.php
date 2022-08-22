<?php

namespace App\Request;


class ExperienceFilterRequest extends BaseRequest
{
    use ValidateRequestTrait;

    public ?string $host = null;
    public ?int $category = null;
    public ?bool $purchasable = null;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}