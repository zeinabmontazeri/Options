<?php

namespace App\Controller\Shop;

use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;
use App\Service\Shop\GetExperiencesByFilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/shop")]
class GetExperiencesController extends AbstractController
{

    #[Route('/experience', name: 'app_get_experiences', methods: ['GET'])]
    public function index(
        Request                       $request,
        ExperienceRepository          $experienceRepository,
        GetExperiencesByFilterService $service): JsonResponse
    {
        $result = $service->getExperience($request->query->all(), $experienceRepository);
        return $this->json($result);

    }
}
