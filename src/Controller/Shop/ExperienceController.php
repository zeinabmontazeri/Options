<?php

namespace App\Controller\Shop;

use App\Auth\AcceptableRoles;
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

#[Route('api/v1/shop')]
class ExperienceController extends AbstractController
{
    #[Route('/experiences', name: 'app_get_experiences', methods: ['GET'])]
    #[AcceptableRoles('ROLE_GUEST')]
    #[Route('/experience', name: 'app_get_experiences', methods: ['GET'])]
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

    #[Route('/experience/{experience_id}/events/', name: 'app_experience_event_list', methods: ['GET'])]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    #[AcceptableRoles('ROLE_GUEST')]
    public function getExperiences(
        Experience                    $experience,
        EventRepository               $eventRepository,
        GetAllExperienceEventsService $getAllExperienceEventsService): JsonResponse
    {
        $result = $getAllExperienceEventsService->getExperienceEvents($experience, $eventRepository);
        return $this->json([
            'data' => $result,
            'message' => "All events successfully retrieved.",
            'status' => 'success',
        ], Response::HTTP_OK);
    }
}
