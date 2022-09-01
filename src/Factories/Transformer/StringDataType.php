<?php

namespace App\Factories\Transformer;

class StringDataType implements DataTypeInterface
{
    public function ConvertToObject(mixed $value)
    {
        return (string) $value;
    }
}