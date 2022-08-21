<?php

namespace App\Controller\Shop;

use App\Entity\Experience;
use App\Repository\EventRepository;
use App\Service\GetAllExperienceEventsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shop')]
class ExperienceEventListController extends AbstractController
{
    #[Route('/experience/events/{id}', name: 'app_experience_event_list', methods: ['GET'])]
    public function index(
        Experience                    $experience,
        EventRepository               $eventRepository,
        GetAllExperienceEventsService $service): JsonResponse
    {
        $events = $service->getExperienceEvents($experience, $eventRepository);
        return new JsonResponse(
            [
                'ok' => true,
                'data' => $events,
                'status' => 200
            ], 200);
    }
}
