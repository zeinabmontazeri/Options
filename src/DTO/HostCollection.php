<?php

namespace App\DTO;

class HostCollection implements CollectionInterface
{
    protected array $result = [];

    public function toArray(array $entities  , array $groups = null): array
    {
        foreach ($entities as $entity) {
            $data['id'] = $entity->getId();
            $data['hostFullName'] = $entity->getFullName();
            $data['approvalStatus'] = $entity->getApprovalStatus()->value;
            $data['level'] = $entity->getLevel()->value;
            $data['createdAt'] = $entity->getCreatedAt();
            $this->result[] = $data;
        }
        return $this->result;
    }

}
