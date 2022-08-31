<?php

namespace App\Factories\Transformer;

class IntegerDataType implements DataType
{
    public function ConvertToObject(mixed $value)
    {
        return (int) $value;
    }
}