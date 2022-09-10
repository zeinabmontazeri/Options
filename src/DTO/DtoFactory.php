<?php

namespace App\DTO;

use PHPUnit\Util\Exception;

class DtoFactory
{

    public static function getInstance(string $collectionType)
    {
        if ($collectionType == 'category')
            return new CategoryCollection();
        if ($collectionType == 'experience')
            return new ExperienceCollection();
        if ($collectionType == 'experienceFilter')
            return new ExperienceFilterCollection();
        if ($collectionType == 'event')
            return new EventCollection();
        if ($collectionType == 'host')
            return new HostCollection();
        else
            throw new Exception("Invalid collection type.", 500);
    }
}