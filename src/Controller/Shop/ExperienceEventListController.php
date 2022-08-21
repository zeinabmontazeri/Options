<?php

namespace App\Controller\Shop;

use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;
use App\Service\GetAllExperienceEventsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shop')]
class ExperienceEventListController extends AbstractController
{
    #[Route('/experience/events/{id}', name: 'app_experience_event_list', methods: ['GET'])]
    public function index(
        Request                       $request,
        EventRepository               $eventRepository,
        ExperienceRepository          $experienceRepository,
        GetAllExperienceEventsService $getAllExperienceEventsService): JsonResponse
    {

        $result = $getAllExperienceEventsService->getExperienceEvents(
            (int)$request->get('id'), $experienceRepository, $eventRepository);
        return new JsonResponse($result);
    }
}
