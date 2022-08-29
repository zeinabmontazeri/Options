<?php

namespace App\DTO;

use PHPUnit\Util\Exception;

class DtoFactory
{

    public static function getInstance(string $collectionType) : CollectionInterface
    {
        if($collectionType == 'category')
            return new CategoryCollection();
        if($collectionType == 'experience')
            return new ExperienceCollection();
        else
            throw new Exception("Invalid collection type." , 500);
    }
}