<?php

namespace App\DTO;

use App\Entity\Experience;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExperienceCollection extends JsonResponse
{
    public function toArray(Experience $experience): array
    {
        return [
            'id' => $experience->getId(),
            'title' => $experience->getTitle(),
            'description' => $experience->getDescription(),
            'createdAt' => $experience->getCreatedAt(),
        ];
    }

}
