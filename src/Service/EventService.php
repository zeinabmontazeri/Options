<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Experience;
use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;
use App\Repository\OrderRepository;
use App\Request\EventRequest;

class EventService
{
    public function __construct(private EventRepository $eventRepository,private EventRequest $eventRequest)
    {
    }

    /**
     * @throws \Exception
     */
    public function create(Experience $experience): Event
    {
        $event = new Event();
        $event->setExperience($experience);
        $event->setStartsAt($this->eventRequest->startsAt);
        $event->setAddress($this->eventRequest->address);
        $event->setCapacity($this->eventRequest->capacity);
        $event->setDuration($this->eventRequest->duration);
        $event->setIsOnline($this->eventRequest->isOnline);
        $event->setLink($this->eventRequest->link);
        $event->setPrice($this->eventRequest->price);

        $this->eventRepository->add($event, true);
        return $event;
    }

    public function getOrders(OrderRepository $repository, Event $event)
    {
        return $repository->findUsersInfoAnEvent($event);
    }
}
