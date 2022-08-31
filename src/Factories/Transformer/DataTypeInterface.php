<?php
namespace App\Factories\Transformer;

interface DataTypeInterface
{
    public function ConvertToObject(mixed $value);
}