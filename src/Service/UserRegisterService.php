<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Request\UserRegisterRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegisterService
{


    public function add(
        UserRegisterRequest         $request,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $hasher): JsonResponse
    {
        $user = new User();
        $user->setPhoneNumber($request->phoneNumber);
        $user->setPassword($hasher->hashPassword($user, $request->password));
        $user->setEmail($request->email);
        $user->setFirstName($request->firstName);
        $user->setLastName($request->lastName);
        $user->setBirthDate(new \DateTime($request->birthDate));
        $user->setCreatedAt((new \DateTimeImmutable)->setTimestamp(time()));
        $userRepository->add($user, true);
        return new JsonResponse(['message' => 'success'], 201);

    }


}

