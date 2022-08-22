<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Experience;
use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;
use App\Request\EventRequest;

class EventService
{
    /**
     * @throws \Exception
     */
    public function create(EventRequest $request, EventRepository $repository, Experience $experience): string
    {
        if ($request->errors){
            throw new \Exception($request->errors);
        }
        $event = new Event();
        $event->setExperience($experience);
        $event->setStartsAt($request->startsAt);
        $event->setAddress($request->address);
        $event->setCapacity($request->capacity);
        $event->setDuration($request->duration);
        $event->setIsOnline($request->isOnline);
        $event->setLink($request->link);
        $event->setPrice($request->price);

//        $repository->add($event, true);
        return "event created successfully with id: {$event->getId()}";
    }
}