<?php

namespace App\DTO;

use App\Entity\Experience;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExperienceCollection implements CollectionInterface
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
