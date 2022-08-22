<?php

namespace App\Auth;

use App\Service\LoginCheckerService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginChecker implements UserCheckerInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private LoginCheckerService $service
    ) {
    }

    public function checkPreAuth(UserInterface $user)
    {
    }

    public function checkPostAuth(UserInterface $user)
    {
        $requestData = $this
            ->requestStack
            ->getCurrentRequest()
            ->toArray();

        $requestRole = $requestData['role'] ?? ROLE_EXPERIENCER;
        $requestRole = strtoupper($requestRole);

        $userRole = $user->getRoles();

        $this->service->check($requestRole, $userRole);
    }
}
