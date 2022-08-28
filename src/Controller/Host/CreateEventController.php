<?php

namespace App\Controller\Host;

use App\Entity\Experience;
use App\Repository\EventRepository;
use App\Request\EventRequest;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CreateEventController extends AbstractController
{
    #[Route('/host/experience/{id}/create/event', name: 'app_host_create_event', methods: ['POST'])]
    public function createEvent(Experience $experience, EventRepository $repository, EventRequest $request, EventService $eventService)
    {
        try {
            $msg = $eventService->create($request, $repository, $experience);
            return new JsonResponse([
                'data' => [],
                'message' => $msg,
                'status' => true
            ]);
        }
        catch (\Exception $e){
            return new JsonResponse([
                'data' => [],
                'message' => $e->getMessage(),
                'status' => false
            ]);
        }
    }
}
