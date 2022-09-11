<?php

namespace App\Service\Shop;

use App\DTO\DtoFactory;
use App\Entity\Experience;
use App\Repository\ExperienceRepository;
use App\Request\ExperienceFilterRequest;

class GetExperiencesByFilterService
{

    public function getExperience(
        ExperienceFilterRequest $experienceFilterRequest,
        ExperienceRepository    $experienceRepository,
    ): array
    {
        $experiencesData = $experienceRepository->filterExperience($experienceFilterRequest);
        $experienceCollection = DtoFactory::getInstance(Experience::class);
        return $experienceCollection->toArray($experiencesData);
    }
}