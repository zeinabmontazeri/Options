<?php

namespace App\Service\Shop;

use App\Repository\ExperienceRepository;
use App\Request\ExperienceFilterRequest;

class GetExperiencesByFilterService
{
    use ApplyExperienceDOTTrait;

    public function getExperience(
        ExperienceFilterRequest $experienceFilterCollection,
        ExperienceRepository    $experienceRepository,
    ): array
    {
        return self::parse($experienceRepository->filteredExperience($experienceFilterCollection));
    }
}