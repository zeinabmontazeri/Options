<?php

namespace App\Listener;

use App\Exception\AuthException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener(
    event: 'kernel.exception',
    method: 'exceptionHandler',
)]
final class AuthExceptionListener
{
    public function exceptionHandler(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof AuthException) {
            $event->setResponse(new Response($exception->getCode().':'.$exception->getMessage()));
        }
    }
}
