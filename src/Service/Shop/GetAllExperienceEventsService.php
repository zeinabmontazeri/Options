<?php

namespace App\Service\Shop;

use App\DTO\DtoFactory;
use App\Entity\Event;
use App\Entity\Experience;
use App\Repository\EventRepository;
use JetBrains\PhpStorm\ArrayShape;

class GetAllExperienceEventsService
{
    #[ArrayShape(['data' => "array", 'status' => "bool", 'message' => "string"])]
    public function getExperienceEvents(
        Experience      $experience,
        EventRepository $eventRepository): array
    {
        $experienceId = $experience->getId();
        $eventsData = $eventRepository->getEventsByExperienceId($experienceId);
        $eventCollection = DtoFactory::getInstance(Event::class);
        return $eventCollection->toArray($eventsData);
    }
}

