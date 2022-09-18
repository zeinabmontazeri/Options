<?php

namespace App\Service\Shop;

use App\DTO\DtoFactory;
use App\Entity\Experience;
use App\Repository\EventRepository;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class GetAllExperienceEventsService
{
    /**
     * @throws ExceptionInterface
     */
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

