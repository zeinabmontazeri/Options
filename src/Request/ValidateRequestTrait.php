<?php

namespace App\Request;
trait ValidateRequestTrait
{
    public function populate(array $fields): void
    {
        foreach ($fields as $field => $value) {
            if (property_exists($this, $field)) {
                if (preg_match('/^\d{4}-\d\d-\d\d( \d\d:\d\d:\d\d)?$/', $value))
                    $this->{$field} = new \DateTime($value);
                else
                    $this->{$field} = $value;
            }
        }
    }
}