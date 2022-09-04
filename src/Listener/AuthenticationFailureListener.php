<?php

namespace App\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AsEventListener(
    event: 'lexik_jwt_authentication.on_authentication_failure',
    method: 'setResponseFormat'
)]
final class AuthenticationFailureListener
{
    public function setResponseFormat(AuthenticationFailureEvent $event)
    {
        $response = new JsonResponse(
            data: [
            'success' => false,
            'data' => [],
            'message' => 'Invalid credentials.',
            ],
            status: JsonResponse::HTTP_UNAUTHORIZED
        );

        $event->setResponse($response);
    }
}