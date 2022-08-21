<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterRequest extends BaseRequest
{

    use ValidateRequestTrait;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Regex(
        pattern: '/^(\+98|0098|0)?9\d{9}$/',
        message: 'Phone number must be 10 digits',
        match: true
    )]
    public readonly ?string $phoneNumber;
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 6)]
    public readonly ?string $password;
    public readonly ?string $firstName;
    public readonly ?string $lastName;
    #[Assert\Email]
    public readonly ?string $email;
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Date]
    public readonly ?string $birthDate;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}