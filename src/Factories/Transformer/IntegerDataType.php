<?php

namespace App\Factories\Transformer;

class IntegerDataType implements DataTypeInterface
{
    public function ConvertToObject(mixed $value)
    {
        return (int) $value;
    }
}