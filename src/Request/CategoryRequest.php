<?php
namespace App\Request;
class CategoryRequest extends BaseRequest
{
    use ValidateRequestTrait;

    public ?int $id = null;

    public ?string $name = null;

    protected function autoValidateRequest(): bool
    {
        return false;
    }
}