<?php

namespace App\Exception;

use Error;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionFactory
{
    public static function getException($exception): JsonResponse
    {
        $exceptionData = [
            'status' => 'failed',
            'data' => [],
        ];
        $response = new JsonResponse();
        if (in_array($exception::class, HttpExceptionEnum::getConstants())) {
            $exceptionData['data'] = $exception->getMessage();
        } else {
            $exceptionData['data'] = "Internal Server Error.";
        }
        $response->setData($exceptionData);

        if ($exception instanceof NotFoundHttpException and
            str_contains($exception->getMessage(), 'object not found by the @ParamConverter')) {
            $response->setStatusCode(400);
        } else if ($exception instanceof Error) {
            $response->setStatusCode(500);
        } else {
            $response->setStatusCode($exception->getStatusCode());
        }
        return $response;
    }
}