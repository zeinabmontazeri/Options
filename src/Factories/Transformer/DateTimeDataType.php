<?php

namespace App\Factories\Transformer;

class DateTimeDataType implements DataType
{
    public function ConvertToObject(mixed $value)
    {
        return new \DateTime($value);
    }
}