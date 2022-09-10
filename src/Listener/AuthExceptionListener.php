<?php

namespace App\Listener;

use App\Exception\AuthException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(
    event: 'kernel.exception',
    method: 'setAuthenticationExceptionResponse',
)]
final class AuthExceptionListener
{
    public function __construct(
        private UrlGeneratorInterface $router,
        private RequestStack $requestStack,
    ) {
    }

    public function setAuthenticationExceptionResponse(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof AuthException) {
            $response = new JsonResponse(
                data: [
                    'status' => 'failed',
                    'data' => [],
                    'message' => $exception->getMessage(),
                ],
                status: $exception->getStatusCode()
            );
        }

        $request = $this->requestStack->getCurrentRequest();
        $uri = $request->getRequestUri();
        if (
            $exception instanceof BadRequestHttpException
            and $uri === $this->router->generate('auth_login')
        ) {
            throw new BadRequestHttpException("'phoneNumber' and 'password' must be provided.");
        }
        if (isset($response)) {
            $event->setResponse($response);
        }
    }
}
