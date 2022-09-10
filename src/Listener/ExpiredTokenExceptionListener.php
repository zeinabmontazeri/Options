<?php

namespace App\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

#[AsEventListener(
    event: 'lexik_jwt_authentication.on_jwt_expired',
    method: 'setExpiredTokenExceptionResponse',
)]
final class ExpiredTokenExceptionListener
{
    public function setExpiredTokenExceptionResponse(JWTExpiredEvent $event): void
    {
        throw new UnauthorizedHttpException('challenge', 'Token expired.');
    }
}
