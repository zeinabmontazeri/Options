<?php

namespace App\Controller\Shop;

use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;
use App\Service\Shop\GetExperiencesByFilterService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/shop")]
class GetExperiencesController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/experiences', name: 'app_get_experiences', methods: ['GET'])]
    public function index(
        Request                       $request,
        EventRepository               $eventRepository,
        ExperienceRepository          $experienceRepository,
        GetExperiencesByFilterService $service): JsonResponse
    {
        $result = $service->getExperience($request->query->all(), $eventRepository, $experienceRepository);
        return $result;

    }
}
