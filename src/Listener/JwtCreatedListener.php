<?php

namespace App\Listener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[AsEventListener(
    event: 'lexik_jwt_authentication.on_jwt_created',
    method: 'setJwtRoleEqualToLoginRole'
)]
final class JwtCreatedListener
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function setJwtRoleEqualToLoginRole(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $requestPayload = $request->toArray();
        $requestRole = $requestPayload['role'] ?? User::ROLE_EXPERIENCER;

        // When jwt is created it means the provided role is correct so
        // it is not necessary to check if it is in roles enum
        $token = $event->getData();
        $token['roles'] = strtoupper($requestRole);

        $event->setData($token);
    }
}
