<?php

namespace App\Service;

use App\DTO\EventCollection;
use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;

class GetAllExperienceEventsService
{
    public function getExperienceEvents(
        $experienceId,
        ExperienceRepository $experienceRepository,
        EventRepository $eventRepository): array
    {
        $experience = $experienceRepository->find($experienceId);
        if (!$experience) {
            return ['ok' => false, 'message' => 'Experience not found.', 'status' => 404];
        }
        $eventsData = $eventRepository->getEventsByExperienceId($experience->getId());
        $result = [];
        foreach ($eventsData as $eventData) {
            $eventCollection = new EventCollection();
            $eventCollection->id = $eventData->getId();
            $eventCollection->price = $eventData->getPrice();
            $eventCollection->capacity = $eventData->getCapacity();
            $eventCollection->duration = $eventData->getDuration();
            $eventCollection->isOnline = $eventData->isIsOnline();
            $eventCollection->startsAt = $eventData->getStartsAt();
            $eventCollection->link = $eventData->getLink();
            $eventCollection->address = $eventData->getAddress();
            $result[] = $eventCollection;
        }
        $result['message'] = "All events of Experience {$experience->getTitle()} successfully returned.";
        $result['ok'] = true;
        $result['status'] = 200;
        return $result;

    }
}

