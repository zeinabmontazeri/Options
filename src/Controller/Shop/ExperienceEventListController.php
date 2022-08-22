<?php

namespace App\Controller\Shop;

use App\Entity\Experience;
use App\Repository\EventRepository;
use App\Service\GetAllExperienceEventsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/shop')]
class ExperienceEventListController extends AbstractController
{
    #[Route('/experience/{experience_id}/events/', name: 'app_experience_event_list', methods: ['GET'])]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    public function index(
        Experience                    $experience,
        EventRepository               $eventRepository,
        GetAllExperienceEventsService $getAllExperienceEventsService): JsonResponse
    {
        $result = $getAllExperienceEventsService->getExperienceEvents($experience, $eventRepository);
        return $this->json([
            'data' => $result['data'],
            'message' => $result['message'],
            'status' => $result['status'],
        ], Response::HTTP_OK);
    }
}
