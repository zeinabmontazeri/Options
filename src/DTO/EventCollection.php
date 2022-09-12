<?php

namespace App\DTO;


class EventCollection implements CollectionInterface
{
    protected array $result = [];

    public function toArray(array $entities  , array $groups = null): array
    {
        foreach ($entities as $entity) {
            $data['id'] = $entity->getId();
            $data['price'] = $entity->getPrice();
            $data['capacity'] = $entity->getCapacity();
            $data['duration'] = $entity->getDuration();
            $data['isOnline'] = $entity->isIsOnline();
            $data['startsAt'] = $entity->getStartsAt();
            $data['link'] = $entity->getLink();
            $data['address'] = $entity->getAddress();
            $data['createdAt'] = $entity->getCreatedAt();
            $this->result[] = $data;
        }
        return $this->result;
    }

}