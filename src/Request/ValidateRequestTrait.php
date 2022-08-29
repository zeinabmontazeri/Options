<?php

namespace App\Request;
use App\Factories\Transformer\DataTypeFactory;

trait ValidateRequestTrait
{
    public function populate(array $fields): void
    {
        $typeFactory = new DataTypeFactory();
        foreach ($fields as $field => $value) {
            if (property_exists($this, $field)) {
                $refProperty = new \ReflectionProperty($this, $field);
                $object = $typeFactory->getObject($refProperty->getType()->getName());
                $this->{$field} = $object->ConvertToObject($value);
            }
        }
    }
}