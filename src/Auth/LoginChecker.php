<?php

namespace App\Auth;

use App\Entity\User;
use App\Service\LoginCheckerService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginChecker implements UserCheckerInterface
{
    public function __construct(
        private RequestStack          $requestStack,
        private LoginCheckerService   $service,
        private UrlGeneratorInterface $router
    )
    {
    }

    public function checkPreAuth(UserInterface $user)
    {
    }

    public function checkPostAuth(UserInterface $user)
    {
        $request = $this
            ->requestStack
            ->getCurrentRequest();


        if ($request->getPathInfo() !== $this->router->generate('auth_login')) {
            return;
        }

        $requestData = $request->toArray();

        $requestRole = $requestData['role'] ?? User::ROLE_EXPERIENCER;
        $requestRole = strtoupper($requestRole);

        $userRole = $user->getRoles();

        if (!$this->service->check($requestRole, $userRole))
            throw new UnauthorizedHttpException('challenge', 'Invalid credentials.');
    }
}
