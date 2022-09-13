<?php

namespace App\Service\Shop;

use App\DTO\DtoFactory;
use App\Entity\Experience;
use App\Repository\EventRepository;

class GetAllExperienceEventsService
{
    public function getExperienceEvents(
        Experience      $experience,
        EventRepository $eventRepository): array
    {
        $experienceId = $experience->getId();
        $eventsData = $eventRepository->getEventsByExperienceId($experienceId);
        $eventCollection = DtoFactory::getInstance();
        return $eventCollection->toArray($eventsData, ['event']);
    }
}

