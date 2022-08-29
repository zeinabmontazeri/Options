<?php

namespace App\Service\Shop;

use App\DTO\EventCollection;
use App\Entity\Experience;
use App\Repository\EventRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetAllExperienceEventsService
{
    #[ArrayShape(['data' => "array", 'status' => "bool", 'message' => "string"])]
    public function getExperienceEvents(
        Experience      $experience,
        EventRepository $eventRepository): array
    {
        $experienceId = $experience->getId();
        $eventsData = $eventRepository->getEventsByExperienceId($experienceId);
        $result = [];
        if (!empty($eventsData)) {
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
                $eventCollection->createdAt = $eventData->getCreatedAt();
                $result['data'][] = $eventCollection;
            }
            $result['message'] = "All events of Experience with id => {$experienceId} successfully retrieved.";
        } else {
            throw new NotFoundHttpException("No events found for Experience with id => {$experienceId}.");
        }
        $result['status'] = 'success';
        return $result;

    }
}

