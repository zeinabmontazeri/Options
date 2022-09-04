<?php

namespace App\Controller\Host;

use App\Auth\AcceptableRoles;
use App\Entity\Event;
use App\Entity\Experience;
use App\Entity\User;
use App\Request\EventRequest;
use App\Service\EventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/hosts')]
class EventController extends AbstractController
{
    #[Route('/experiences/{experience_id}/events/create', name: 'app_host_create_event', methods: ['POST'])]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function create(Experience $experience, EventService $eventService, EventRequest $eventRequest): JsonResponse
    {
        $createdEvent = $eventService->create($experience, $eventRequest);
        return $this->json([
            'data' => [
                'id' => $createdEvent->getId()
            ],
            'message' => "event created successfully",
            'status' => 'success',
            'code' => Response::HTTP_CREATED
        ]);
    }

    #[Route('/events/{event_id}/report', name: 'app_host_event_report', methods: ['GET'])]
    #[ParamConverter('event', class: Event::class, options: ['id' => 'event_id'])]
    #[AcceptableRoles(User::ROLE_HOST, User::ROLE_ADMIN)]
    public function getReport(Event $event, EventService $service): JsonResponse
    {
        return $this->json($service->getOrdersInfo($event));
    }
}
