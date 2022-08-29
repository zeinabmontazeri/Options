<?php

namespace App\Auth;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthenticatedUser
{
    public function __construct(
        protected JWTTokenManagerInterface $JWTTokenManager,
        protected TokenStorageInterface    $tokenStorage,
        protected UserRepository           $repository,
    )
    {
    }

    /**
     * @throws JWTDecodeFailureException
     */
    public function getUser()
    {
        $payload = $this->JWTTokenManager->decode($this->tokenStorage->getToken());
        return $this->repository->findOneBy(['phoneNumber' => $payload['username']]);
    }


    public function getRole()
    {
        $payload = $this->JWTTokenManager->decode($this->tokenStorage->getToken());
        return $payload['roles'];
    }
}