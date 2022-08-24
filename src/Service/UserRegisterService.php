<?php

namespace App\Service;

use App\Entity\Host;
use App\Entity\User;
use App\Repository\HostRepository;
use App\Repository\UserRepository;
use App\Request\UserRegisterRequest;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegisterService
{


    public function register(
        UserRegisterRequest         $request,
        UserRepository              $userRepository,
        EntityManager               $em,
        UserPasswordHasherInterface $hasher): User
    {
        //Check if user already exists
        if($userRepository->checkExistsByPhoneNumber($request->phoneNumber))
            throw new Exception('User Already Exists');


        //Check if birtdate is not in the feature
        $birthDate = new \DateTime($request->birthDate);
        if($birthDate > (new \DateTime()))
            throw new Exception("Birthday is not in range");

        $em->getConnection()->beginTransaction(); // suspend auto-commit
        try {
            $user = new User;
            $user->setPhoneNumber($request->phoneNumber)
            ->setFirstName($request->firstName)
            ->setLastName($request->lastName)
            ->setBirthDate($birthDate)
            ->setGender($request->gender)
            ->setRoles([$request->role])
            ->setPassword($hasher->hashPassword($user, $request->password));

            $em->persist($user);
            $em->flush();

            if($request->role==="ROLE_HOST"){
                $host = new Host();
                $host->setUser($user);
                $em->persist($host);
                $em->flush();
            }
            $em->getConnection()->commit();
            return $user;

        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }
}

