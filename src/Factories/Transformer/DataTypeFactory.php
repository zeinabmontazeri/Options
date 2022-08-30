<?php

namespace App\Factories\Transformer;

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
        throw new \Exception("type not found");
    }
}