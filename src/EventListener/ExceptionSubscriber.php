<?php
namespace App\EventListener;

use App\Exception\ValidationException;
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

        $exceptionData = [
            'success' => false,
            'data' => [] ,
            'message' => "",
        ];
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationException) {
            $exceptionData['data'] = $exception->getMessages();
            $exceptionData['message'] = $exception->getMessage();
        } else if ($exception instanceof NotFoundHttpException) {
            $exceptionData['message'] = "Invalid query parameters";
        }else {
            $exceptionData['message'] = "Bad Request: ".$exception->getMessage();
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

    #[ArrayShape([KernelEvents::EXCEPTION => "string"])]
    public static function getSubscribedEvents()
    {
       return [
           KernelEvents::EXCEPTION => 'onKernelException'
       ];
    }
}