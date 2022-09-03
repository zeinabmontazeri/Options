<?php

namespace App\Factories\Transformer;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DataTypeFactory
{
    public function getObject(string $type)
    {
        if ($type == 'DateTimeInterface')
            return new DateTimeDataType();
        if ($type == 'int')
            return new IntegerDataType();
        if ($type == 'string')
            return new StringDataType();
        if ($type == 'bool')
            return new BooleanDataType();
        throw new NotFoundHttpException();
    }
}