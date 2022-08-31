<?php

namespace App\Factories\Transformer;

class StringDataType implements DataType
{
    public function ConvertToObject(mixed $value)
    {
        return (string) $value;
    }
}