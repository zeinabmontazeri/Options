<?php

namespace App\Factories\Transformer;

class DateTimeDataType implements DataTypeInterface
{
    public function ConvertToObject(mixed $value)
    {
        return new \DateTime($value);
    }
}