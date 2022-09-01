<?php

namespace App\Service\Shop;

use App\DTO\ExperienceFilterCollection;

trait ApplyExperienceDTOTrait
{

    public function parse(array $experiences): array
    {
        $experiencesData = [];
        foreach ($experiences as $experience) {
            $experienceCollection = new ExperienceFilterCollection();
            $experienceCollection->id = $experience->getId();
            $experienceCollection->title = $experience->getTitle();
            $experienceCollection->category = [
                'categoryId' => $experience->getCategory()->getId(),
                'categoryName' => $experience->getCategory()->getName()];
            $experienceCollection->description = $experience->getDescription();
            $experienceCollection->host = [
                'hostId' => $experience->getHost()->getId(),
                'hostName' => $experience->getHost()->getFullName()];
            $experienceCollection->media = $experience->getMedia();
            $experienceCollection->createdAt = $experience->getCreatedAt();
            $experiencesData[] = $experienceCollection;
        }
        return $experiencesData;
    }
}