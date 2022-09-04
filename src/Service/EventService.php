<?php

namespace App\Service;

use App\Entity\EnumEventStatus;
use App\Entity\Event;
use App\Entity\Experience;
use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;
use App\Repository\OrderRepository;
use App\Request\EventPublishRequest;
use App\Request\EventRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class EventService
{
    public function __construct(private EventRepository $eventRepository,
                                private OrderRepository $orderRepository,
                                private Security $security,
                                private EntityManagerInterface $entityManager)
    {
    }


    public function create(Experience $experience, EventRequest $eventRequest): Event
    {
        if ($this->security->getUser() !== $experience->getHost()->getUser())
            throw new AccessDeniedException();

        $event = new Event();
        $event->setExperience($experience);
        $event->setStartsAt($eventRequest->startsAt);
        $event->setAddress($eventRequest->address);
        $event->setCapacity($eventRequest->capacity);
        $event->setDuration($eventRequest->duration);
        $event->setIsOnline($eventRequest->isOnline);
        $event->setLink($eventRequest->link);
        $event->setPrice($eventRequest->price);

        $this->eventRepository->add($event, true);
        return $event;
    }

    public function getOrdersInfo(Event $event)
    {
        if ($this->security->getUser() !== $event->getExperience()->getHost()->getUser())
            throw new AccessDeniedException();

        $totalIncome = $this->eventRepository->getTotalIncome($event);
        $usersInfo = $this->eventRepository->findUsersInfoCheckoutedOrders($event);
        return [
            'data' => [
                "Event Data" => [
                    "Experience Title" => $event->getExperience()->getTitle(),
                    "Event Capacity" => $event->getCapacity(),
                    "Event Remaining Capacity" => $event->getCapacity() - count($usersInfo)
                ],
                "Total Income" => $totalIncome,
                "List of users Information" => $usersInfo
            ],
            'message' => "show orders information for this event is successfully",
            'status' => 'success',
            'code' => Response::HTTP_OK
        ];
    }

    public function changeStatus(Event $event,EventPublishRequest $request): bool
    {
        if ($this->security->getUser() !== $event->getExperience()->getHost()->getUser())
            throw new AccessDeniedException();

        $newStatus = EnumEventStatus::from($request->status);
        //Check if status have been changed
        if($newStatus != $event->getStatus()) {
            //Check if we can change status for this event
            if($newStatus != EnumEventStatus::PUBLISHED){
                $completedOrders = $this->orderRepository->getCompletedOrders($event);
                if(sizeof($completedOrders)!=0){
                    throw new BadRequestHttpException("You can't change status because this event has completed order");
                }
            }

            $event->setStatus($newStatus);
            $this->entityManager->persist($event);
            $this->entityManager->flush();
        }


        return true;
    }
}
