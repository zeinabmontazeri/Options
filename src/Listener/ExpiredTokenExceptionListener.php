<?php

namespace App\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AsEventListener(
    event: 'lexik_jwt_authentication.on_jwt_expired',
    method: 'setExpiredTokenExceptionResponse',
)]
final class ExpiredTokenExceptionListener
{
    public function setExpiredTokenExceptionResponse(JWTExpiredEvent $event): void
    {
        $response = new JsonResponse(
            data: [
                'status' => false,
                'data' => [],
                'message' => 'Token Expired.',
            ],
            status: JsonResponse::HTTP_UNAUTHORIZED
        );

        $event->setResponse($response);
    }
}
