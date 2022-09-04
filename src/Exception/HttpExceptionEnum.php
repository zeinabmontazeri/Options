<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

enum HttpExceptionEnum: string
{
    case BadRequestHttpException = BadRequestHttpException::class;
    case UnauthorizedHttpException = UnauthorizedHttpException::class;
    case AuthorizationException = AuthorizationException::class;
    case AccessDeniedHttpException = AccessDeniedHttpException::class;
    case NotFoundHttpException = NotFoundHttpException::class;
    case MethodNotAllowedHttpException = MethodNotAllowedHttpException::class;
    case ValidationException = ValidationException::class;
    case HttpException = HttpException::class;


    public static function getConstants(): array
    {
        return [
            self::BadRequestHttpException->getValue(),
            self::UnauthorizedHttpException->getValue(),
            self::AuthorizationException->getValue(),
            self::AccessDeniedHttpException->getValue(),
            self::NotFoundHttpException->getValue(),
            self::MethodNotAllowedHttpException->getValue(),
            self::ValidationException->getValue(),
            self::HttpException->getValue()];

    }

    private function getValue(): string
    {
        return $this->value;
    }
}