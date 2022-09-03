<?php
namespace App\Factories\Transformer;

interface DataType
{
    public function ConvertToObject(mixed $value);
}