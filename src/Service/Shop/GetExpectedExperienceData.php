<?php

namespace App\Service\Shop;

trait GetExpectedExperienceData
{

    public function parse(array $experiences): array
    {
        $experiencesData = [];
        foreach ($experiences as $experience) {
            $experienceData['id'] = $experience->getId();
            $experienceData['title'] = $experience->getTitle();
            $experienceData['category'] = $experience->getCategory()->getName();
            $experienceData['description'] = $experience->getDescription();
            $experienceData['host'] = $experience->getHost()->getUser()->getFirstName() . "-" . $experience->getHost()->getUser()->getLastName();
            $experienceData['media'] = $experience->getMedia();
            $experienceData['createdAt'] = $experience->getCreatedAt();
            $experiencesData [] = $experienceData;
        }
        return $experiencesData;
    }
}