<?php

namespace App\Controller\Shop;

use App\Repository\ExperienceRepository;
use App\Request\ExperienceFilterRequest;
use App\Service\Shop\GetExperiencesByFilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/shop")]
class GetExperiencesController extends AbstractController
{

    #[Route('/experience', name: 'app_get_experiences', methods: ['POST'])]
    public function index(
        ExperienceRepository          $experienceRepository,
        GetExperiencesByFilterService $service,
        ExperienceFilterRequest       $experienceFilterCollection,
    ): JsonResponse
    {
        $result = $service->getExperience($experienceFilterCollection, $experienceRepository);
        return $this->json(
            [
                'data' => $result,
                'message' => 'Experiences Successfully Retrieved',
                'status' => true,
            ], 200
        );
    }
}
