<?php

namespace App\DTO;

use App\Repository\MediaRepository;

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
            $data['media'] = $entity->getMediaFileNames();
            $this->result[] = $data;
        }
        return $this->result;
    }

}
