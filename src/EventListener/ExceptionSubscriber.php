<?php

namespace App\EventListener;

use App\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
class ExceptionSubscriber implements EventSubscriberInterface
{

    public function onKernelException(ExceptionEvent $event)
    {
        $exceptionData = [
            'success' => false,
            'data' => [],
        ];
        $exception = $event->getThrowable();
        if ($exception instanceof ValidationException) {
            $exceptionData['data'] = $exception->getMessages();
            $exceptionData['message'] = $exception->getMessage();
        } else if ($exception instanceof NotFoundHttpException) {
            if (str_contains($exception->getMessage(), 'object not found by the @ParamConverter'))
                $exceptionData['message'] = 'Resource not found.';
            else
                $exceptionData['message'] = $exception->getMessage();
        } else if ($exception instanceof AccessDeniedHttpException) {
            $exceptionData['message'] = $exception->getMessage();
        } else {
            $exceptionData['message'] = "Bad Request: " . $exception->getMessage();
        }
        $response = new JsonResponse();
        $response->setData($exceptionData);
        if ($exception instanceof HttpException) {
            $response->setStatusCode(400);
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $event->setResponse($response);
    }
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }
}