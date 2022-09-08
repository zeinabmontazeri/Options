<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterRequest extends BaseRequest
{

    use ValidateRequestTrait;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Regex(
        pattern: '/^09\d{9}$/',
        message: 'Phone number has wrong format',
        match: true
    )]
    public readonly ?string $phoneNumber;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Regex(
        pattern: '/^[ آابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهییئa-zA-Z]*$/',
        message: 'Only letters are allowed',
        match: true
    )]
    public readonly ?string $firstName;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Regex(
        pattern: '/^[ آابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهییئa-zA-Z]*$/',
        message: 'Only letters are allowed',
        match: true
    )]
    public readonly ?string $lastName;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\LessThan(new \DateTime())]
    public readonly ?\DateTimeInterface $birthDate;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 6)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z@#$%!\s]*/',
        message: 'Password has wrong format',
        match: true
    )]
    public readonly ?string $password;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(['MALE', 'FEMALE'])]
    public readonly ?string $gender;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(["ROLE_EXPERIENCER", "ROLE_HOST"])]
    public readonly ?string $role;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}