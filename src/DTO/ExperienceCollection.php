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
            $data['category'] = [
                'categoryId' => $entity->getCategory()->getId(),
                'categoryName' => $entity->getCategory()->getName()];
            $data['description'] = $entity->getDescription();
            $data['host'] = [
                'hostId' => $entity->getHost()->getId(),
                'hostName' => $entity->getHost()->getFullName()];
            $data['createdAt'] = $entity->getCreatedAt();
            $data['media'] = $entity->getMediaFileNames();
            $data['status'] = $entity->getStatus();
            $this->result[] = $data;
        }
        return $this->result;
    }

}
