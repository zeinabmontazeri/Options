<?php

namespace App\Factories\Transformer;

class FileDataType implements DataType
{
    public function ConvertToObject(mixed $value)
    {
        return $value;
    }
}