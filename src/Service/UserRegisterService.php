<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Request\UserRegisterRequest;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegisterService
{


    public function register(
        UserRegisterRequest         $request,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $hasher): User
    {
        //Check if user already exists
        $existing_user = $userRepository->findOneBy(['phoneNumber'=>$request->phoneNumber]);
        if($existing_user)
            throw new Exception('User Already Exists');


        //Check if birtdate is not in the feature
        $birthDate = new \DateTime($request->birthDate);
        if($birthDate > (new \DateTime()))
            throw new Exception("Birthday is not in range");


        $user = new User();
        $user->setPhoneNumber($request->phoneNumber);
        $user->setFirstName($request->firstName);
        $user->setLastName($request->lastName);
        $user->setBirthDate($birthDate);
        $user->setGender($request->gender);
        $user->setRoles([$request->role]);
        $user->setPassword($hasher->hashPassword($user, $request->password));
        $userRepository->add($user, true);
        return $user;
    }


}

