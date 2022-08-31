<?php

namespace App\DTO;

class CategoryCollection implements CollectionInterface
{
    protected array $result = [];

    public function toArray(array $entities): array
    {
        foreach ($entities as $entity) {
            $data['id'] = $entity->getId();
            $data['name'] = $entity->getName();
            $this->result[] = $data;
        }
        return $this->result;
    }
}