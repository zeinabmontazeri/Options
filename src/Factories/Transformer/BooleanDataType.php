<?php

namespace App\Factories\Transformer;

class BooleanDataType implements DataType
{
    public function ConvertToObject(mixed $value)
    {
        return (bool) $value;
    }
}