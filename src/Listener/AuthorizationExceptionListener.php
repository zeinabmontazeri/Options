<?php

namespace App\Listener;

use App\Exception\AuthorizationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsEventListener(
    event: 'kernel.exception',
    method: 'setAuthorizationExceptionResponse',
)]
final class AuthorizationExceptionListener
{
    public function setAuthorizationExceptionResponse(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (
            $exception instanceof HttpException
            and $exception->getMessage() === 'AUTHORIZATION_FAILED'
        ) {
            $response = new JsonResponse(
                data: [
                    'status' => false,
                    'data' => [],
                    'message' => 'Access denied.',
                ],
                status: $exception->getStatusCode()
            );
        }

        if (
            $exception instanceof AuthorizationException
            and $exception->getMessage() === 'INVALID_ROLE_ATTRIBUTE_ON_CONTROLLER'
        ) {
            $response = new JsonResponse(
                data: [
                    'status' => false,
                    'data' => [],
                    'message' => 'Invalid controller setting.',
                ],
                status: $exception->getStatusCode()
            );
        }

        if (isset($response)) {
            $event->setResponse($response);
        }
    }
}
