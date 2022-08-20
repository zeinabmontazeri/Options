<?php

namespace App\Request;
trait ValidateRequestTrait
{
    public function populate(array $fields): void
    {
        foreach ($fields as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }
    }
}