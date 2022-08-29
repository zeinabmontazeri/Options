<?php
namespace App\DTO;

interface CollectionInterface
{
    public function toArray(array $entities):array;
}