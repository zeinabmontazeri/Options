<?php

namespace App\Service\Shop;

use App\DTO\DtoFactory;
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
        $experienceCollection = DtoFactory::getInstance();
        return $experienceCollection->toArray($experiencesData , ['experiencer']);
    }
}