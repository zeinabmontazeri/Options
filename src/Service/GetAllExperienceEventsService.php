<?php

namespace App\Service;

use App\Entity\Experience;
use App\Repository\EventRepository;

class GetAllExperienceEventsService
{
    public function getExperienceEvents(
        Experience      $experience,
        EventRepository $eventRepository): array
    {
        $eventsData = $eventRepository->getEventsByExperienceId($experience->getId());
        $events = [];
        for ($i = 0; $i < sizeof($eventsData); $i++) {
            $events[$i]['id'] = $eventsData[$i]->getId();
            $events[$i]['price'] = $eventsData[$i]->getPrice();
            $events[$i]['capacity'] = $eventsData[$i]->getCapacity();
            $events[$i]['duration'] = $eventsData[$i]->getDuration();
            $events[$i]['isOnline'] = $eventsData[$i]->isIsOnline();
            $events[$i]['StartsAt'] = $eventsData[$i]->getStartsAt();
            $events[$i]['link'] = $eventsData[$i]->getLink();
            $events[$i]['address'] = $eventsData[$i]->getAddress();
            $events[$i]['createdAt'] = $eventsData[$i]->getCreatedAt();
        }
        return $events;

    }
}

