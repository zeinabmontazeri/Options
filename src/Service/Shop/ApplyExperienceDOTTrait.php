<?php

namespace App\Service\Shop;

use App\DTO\ExperienceCollection;

trait ApplyExperienceDOTTrait
{

    public function parse(array $experiences): array
    {
        $experiencesData = [];
        foreach ($experiences as $experience) {
            $experienceCollection = new ExperienceCollection();
            $experienceCollection->id = $experience->getId();
            $experienceCollection->title = $experience->getTitle();
            $experienceCollection->category = $experience->getCategory()->getName();
            $experienceCollection->description = $experience->getDescription();
            $experienceCollection->host = $experience->getHost()->getFullName();
            $experienceCollection->media = $experience->getMedia();
            $experienceCollection->createdAt = $experience->getCreatedAt();
            $experiencesData[] = $experienceCollection;
        }
        return $experiencesData;
    }
}