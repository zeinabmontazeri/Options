<?php

namespace App\DTO;

class ExperienceCollection implements CollectionInterface
{
    protected array $result = [];

    public function toArray($entities): array
    {
        foreach ($entities as $entity) {
            $data['id'] = $entity->getId();
            $data['title'] = $entity->getTitle();
            $data['description'] = $entity->getDescription();
            $data['createdAt'] = $entity->getCreatedAt();
            $this->result[] = $data;
        }
        return $this->result;
    }

}
