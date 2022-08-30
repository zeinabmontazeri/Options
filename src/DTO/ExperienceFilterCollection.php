<?php

namespace App\DTO;

class ExperienceFilterCollection implements CollectionInterface
{
    protected array $result = [];

    public function toArray($entities): array
    {
        foreach ($entities as $entity) {
            $data['id'] = $entity->getId();
            $data['title'] = $entity->getTitle();
            $data['category'] = [
                'categoryId' => $entity->getCategory()->getId(),
                'categoryName' => $entity->getCategory()->getName()];
            $data['description'] = $entity->getDescription();
            $data['host'] = [
                'hostId' => $entity->getHost()->getId(),
                'hostName' => $entity->getHost()->getFullName()];
            $data['media'] = $entity->getMedia();
            $data['createdAt'] = $entity->getCreatedAt();
            $this->result[] = $data;
        }
        return $this->result;
    }
}
