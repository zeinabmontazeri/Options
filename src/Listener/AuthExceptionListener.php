<?php

namespace App\Listener;

use App\Exception\AuthException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

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
        }


        $request = Request::createFromGlobals();
        $uri = $request->getRequestUri();
        if (
            $exception instanceof BadRequestHttpException
            and $uri === '/api/v1/auth/login'
        ) {
            $previous = $exception->getPrevious();
            if ($previous instanceof NoSuchPropertyException) {
                $response = new JsonResponse(
                    data: [
                        'status' => false,
                        'data' => [],
                        'message' => "'phoneNumber' and 'password' must be provided."
                    ],
                    status: Response::HTTP_BAD_REQUEST
                );
            }
        }

        if (isset($response)) {
            $event->setResponse($response);
        }
    }
}
