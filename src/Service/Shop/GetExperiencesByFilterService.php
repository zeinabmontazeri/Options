<?php

namespace App\Service\Shop;

use App\Repository\ExperienceRepository;
use App\Request\ExperienceFilterRequest;

class GetExperiencesByFilterService
{
    use ApplyExperienceDTOTrait;

    public function getExperience(
        ExperienceFilterRequest $experienceFilterCollection,
        ExperienceRepository    $experienceRepository,
    ): array
    {
        return self::parse($experienceRepository->filterExperience($experienceFilterCollection));
    }
}