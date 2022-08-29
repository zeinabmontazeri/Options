<?php
namespace App\Request;
use Symfony\Component\Validator\Constraints as Assert;
class CategoryRequest extends BaseRequest
{
    use ValidateRequestTrait;

    #[Assert\NotBlank]
    public ?string $name = null;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}