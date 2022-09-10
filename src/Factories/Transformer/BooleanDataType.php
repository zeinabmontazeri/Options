<?php

namespace App\Factories\Transformer;

class BooleanDataType implements DataTypeInterface
{
    public function ConvertToObject(mixed $value)
    {
        return (bool) $value;
    }
}