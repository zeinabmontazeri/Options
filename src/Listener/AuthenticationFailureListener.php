<?php

namespace App\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

#[AsEventListener(
    event: 'lexik_jwt_authentication.on_authentication_failure',
    method: 'setResponseFormat'
)]
final class AuthenticationFailureListener
{
    public function setResponseFormat(AuthenticationFailureEvent $event): void
    {
        throw new UnauthorizedHttpException('challenge', 'Invalid Credentials.');
    }
}