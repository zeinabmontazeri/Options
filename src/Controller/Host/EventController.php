<?php

namespace App\Controller\Host;

use App\Auth\AcceptableRoles;
use App\Entity\Experience;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Request\EventRequest;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/api/v1/host/experience/{id}/event/create', name: 'app_host_create_event', methods: ['POST'])]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function createEvent(Experience $experience, EventService $eventService): JsonResponse
    {
        $createdEvent = $eventService->create($experience);
        return new JsonResponse([
            'data' => [],
            'message' => "event created successfully with id: {$createdEvent->getId()}",
            'status' => true
        ]);
    }
}
