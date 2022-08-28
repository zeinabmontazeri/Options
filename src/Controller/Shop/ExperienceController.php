<?php

namespace App\Controller\Shop;

use App\Entity\Experience;
use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;
use App\Request\ExperienceFilterRequest;
use App\Service\Shop\GetAllExperienceEventsService;
use App\Service\Shop\GetExperiencesByFilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('shop')]
class ExperienceController extends AbstractController
{
    #[Route('/experiences', name: 'app_get_experiences', methods: ['GET'])]
    public function filterExperiences(
        Request                       $request,
        ExperienceRepository          $experienceRepository,
        GetExperiencesByFilterService $service,
        ExperienceFilterRequest       $experienceFilterRequest,
    ): JsonResponse
    {


        $result = $service->getExperience($experienceFilterRequest, $experienceRepository);
        return $this->json(
            [
                'data' => $result,
                'message' => 'Experiences Successfully Retrieved',
                'status' => true,
            ], Response::HTTP_OK
        );
    }

    #[Route('/experiences/{experience_id}/events/', name: 'app_experience_event_list', methods: ['GET'])]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    public function getExperiences(
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
