<?php

namespace App\Exception;

use Error;
use ErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionFactory
{
    public static function getException($exception): JsonResponse
    {
        $exceptionData = [
            'status' => 'failed',
            'message' => [],
        ];
        $response = new JsonResponse();
        if (in_array($exception::class, HttpExceptionEnum::getConstants())) {
            if($exception instanceof BadRequestHttpException)
            {
                $exceptionData['details'] = null;
            }
            if ($exception instanceof ValidationException) {
                $exceptionData['details'] = $exception->getMessages();
                $exceptionData['message'] = 'Validation failed on provided data';
            } else if (str_contains($exception->getMessage(), 'object not found by the @ParamConverter')) {
                $exceptionData['message'] = 'Object not found';
                $exceptionData['details'] = null;
            } else {
                $exceptionData['message'] = $exception->getMessage();
            }

        } else {
            $exceptionData['message'] = "Internal Server Error.";
        }
        $response->setData($exceptionData);

        if ($exception instanceof NotFoundHttpException and
            str_contains($exception->getMessage(), 'object not found by the @ParamConverter')) {
            $response->setStatusCode(400);
        } else if ($exception instanceof Error) {
            $response->setStatusCode(500);
        } else if ($exception instanceof ErrorException) {
            $response->setStatusCode(400);
        } else {
            $response->setStatusCode($exception->getStatusCode());
        }
        return $response;
    }
}