<?php
namespace App\EventListener;

use App\Exception\InvalidInputException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;



class ExceptionSubscriber implements EventSubscriberInterface
{

    public function onKernelException(ExceptionEvent $event )
    {
        $exception = $event->getThrowable();

        if($exception instanceof InvalidInputException)
        {
            $message=$exception->message;
        }elseif (!$exception instanceof NotFoundHttpException) {
            $message = "Bad Request";
        }else {
            $message = "Invalid query parameters";
        }

        $response = new JsonResponse();
        $response->setData([
            'data' => [] ,
            'message' => $message,
            'status' => false
        ]);
        if($exception instanceof InvalidInputException)
        {
            $response->setStatusCode(400);
        }
        elseif ($exception instanceof HttpException) {
            $response->setStatusCode(400);
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $event->setResponse($response);
    }

    #[ArrayShape([KernelEvents::EXCEPTION => "string"])]
    public static function getSubscribedEvents()
    {
       return [
           KernelEvents::EXCEPTION => 'onKernelException'
       ];
    }
}