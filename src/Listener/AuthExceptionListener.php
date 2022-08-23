<?php

namespace App\Listener;

use App\Exception\AuthException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsEventListener(
    event: 'kernel.exception',
    method: 'setAuthenticationExceptionResponse',
)]
final class AuthExceptionListener
{
    public function setAuthenticationExceptionResponse(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AuthException) {
            $response = new JsonResponse(
                data: [
                    'status' => false,
                    'data' => [],
                    'message' => $exception->getMessage(),
                ],
                status: $exception->getStatusCode()
            );
        } elseif ($exception instanceof BadRequestHttpException) {
            $message = $exception->getMessage();
            if(preg_match('/^The key \"(?:phoneNumber|password)\" must be provided.$/', $message))
            {
                $response = new JsonResponse(
                    data:[
                        'status' => false,
                        'data' => [],
                        'message' => '"phoneNumber" and "password" must be provided.'
                    ], 
                    status:JsonResponse::HTTP_BAD_REQUEST
                );
            }
        }

        if (isset($response)) {
            $event->setResponse($response);
        }
    }
}
