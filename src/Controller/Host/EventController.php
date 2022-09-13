<?php

namespace App\Controller\Host;

use App\Auth\AcceptableRoles;
use App\Entity\Event;
use App\Entity\Experience;
use App\Entity\User;
use App\Request\EventRequest;
use App\Request\EventUpdateRequest;
use App\Service\EventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/host')]
class EventController extends AbstractController
{
    #[Route('/experiences/{experience_id}/events', name: 'app_host_create_event', methods: ['POST'])]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function create(Experience $experience, EventService $eventService, EventRequest $eventRequest): JsonResponse
    {
        $createdEvent = $eventService->create($experience, $eventRequest);
        return $this->json([
            'data' => [
                'id' => $createdEvent->getId()
            ],
            'message' => "Event created successfully",
            'status' => 'success'
        ], Response::HTTP_OK);
    }

    #[Route('/events/{event_id}/report', name: 'app_host_event_report', methods: ['GET'])]
    #[ParamConverter('event', class: Event::class, options: ['id' => 'event_id'])]
    #[AcceptableRoles(User::ROLE_HOST, User::ROLE_ADMIN)]
    public function getReport(Event $event, EventService $service): JsonResponse
    {
        return $this->json($service->getOrdersInfo($event));
    }

    #[Route('/experiences/{experience_id}/events/{event_id}', name: 'app_host_update_event', methods: ['PATCH'])]
    #[ParamConverter('event', class: Event::class, options: ['id' => 'event_id'])]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function update(Experience $experience, Event $event, EventService $eventService, EventUpdateRequest $updateRequest): JsonResponse
    {
        $updatedEvent = $eventService->update($experience, $event, $updateRequest);
        return $this->json([
            'data' => [
                'id' => $updatedEvent->getId()
            ],
            'message' => "event updated successfully",
            'status' => 'success'
        ], Response::HTTP_OK);
    }

    #[Route('/experiences/{experience_id}/events/{event_id}', name: 'app_host_delete_event', methods: ['DELETE'])]
    #[ParamConverter('event', class: Event::class, options: ['id' => 'event_id'])]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function delete(Experience $experience, Event $event, EventService $eventService): JsonResponse
    {
        $eventService->delete($experience, $event);
        return $this->json([
            'data' => [],
            'message' => "event deleted successfully",
            'status' => 'success'
        ], Response::HTTP_OK);
    }
}
