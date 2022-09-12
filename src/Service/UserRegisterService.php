<?php

namespace App\Service;

use App\Entity\Host;
use App\Entity\UpgradeRequest;
use App\Entity\User;
use App\Entity\UserUpgradeRequest;
use App\Repository\UserRepository;
use App\Request\UserRegisterRequest;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegisterService
{
    function __construct(
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface      $entityManager
    )
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function register(UserRegisterRequest $request): User
    {
        //Check if user already exists
        if ($this->userRepository->checkExistsByPhoneNumber($request->phoneNumber))
            throw new Exception('User Already Exists');


        $this->entityManager->getConnection()->beginTransaction(); // suspend auto-commit
        try {
            $user = new User;
            $user->setPhoneNumber($request->phoneNumber)
            ->setFirstName($request->firstName)
            ->setLastName($request->lastName)
            ->setBirthDate($request->birthDate)
            ->setGender($request->gender)
            ->setRoles([$request->role])
            ->setPassword($this->hasher->hashPassword($user, $request->password));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            if ($request->role === "ROLE_HOST") {
                $req = new UpgradeRequest();
                $req->setUser($user);
                $req->setMessage("Wants to be host as new user");

                $this->entityManager->persist($req);
                $this->entityManager->flush();
            }
            $this->entityManager->getConnection()->commit();
            return $user;

        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }
}

