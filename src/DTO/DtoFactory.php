<?php

namespace App\DTO;

use PHPUnit\Util\Exception;

class DtoFactory
{

    public static function getInstance(string $collectionType)
    {
        $path = explode('\\', $collectionType);
        $collection = $path[0] . '\DTO\\' . $path[2] . 'Collection';
        if (class_exists($collection))
            return new $collection();
        else
            throw new Exception("Invalid collection type.", 500);
    }
}