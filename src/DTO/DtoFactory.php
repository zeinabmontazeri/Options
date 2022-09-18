<?php

namespace App\DTO;

class DtoFactory
{

    public static function getInstance(): Collection
    {
        return new Collection();
    }
}