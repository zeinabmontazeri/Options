<?php

namespace App\Controller\Shop;

use App\Auth\AcceptableRoles;
use App\Entity\Experience;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;
use App\Request\ExperienceFilterRequest;
use App\Request\ExperienceSearchRequest;
use App\Service\ExperienceService;
use App\Service\Shop\GetAllExperienceEventsService;
use App\Service\Shop\GetExperiencesByFilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/v1')]
class ExperienceController extends AbstractController
{

    #[Route('/experiences', name: 'app_get_experiences', methods: ['GET'])]
    #[AcceptableRoles(User::ROLE_GUEST, User::ROLE_EXPERIENCER, User::ROLE_ADMIN, User::ROLE_HOST)]
    public function filterExperiences(
        ExperienceRepository          $experienceRepository,
        GetExperiencesByFilterService $service,
        ExperienceFilterRequest       $experienceFilterRequest,
        ExperienceSearchRequest       $searchRequest,
        ExperienceService             $experienceService
    ): JsonResponse
    {
        if ($searchRequest->word)
            $result = $experienceService->search($experienceRepository, $searchRequest);
        else
            $result = $service->getExperience($experienceFilterRequest, $experienceRepository);
        return $this->json(
            [
                'data' => $result,
                'message' => 'Experiences Successfully Retrieved.',
                'status' => 'success',
            ],
            Response::HTTP_OK
        );
    }

    #[Route('/experiences/{experience_id}/events', name: 'app_experience_event_list', methods: ['GET'])]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    #[AcceptableRoles(User::ROLE_GUEST, User::ROLE_EXPERIENCER, User::ROLE_ADMIN, User::ROLE_HOST)]
    public function getExperiences(
        Experience                    $experience,
        EventRepository               $eventRepository,
        GetAllExperienceEventsService $getAllExperienceEventsService
    ): JsonResponse
    {
        $result = $getAllExperienceEventsService->getExperienceEvents($experience, $eventRepository);
        return $this->json([
            'data' => $result,
            'message' => "All events successfully retrieved.",
            'status' => 'success',
        ], Response::HTTP_OK);
    }

    #[Route('/experiences/trending', name: 'app_trending_experience', methods: ['GET'])]
    #[AcceptableRoles(User::ROLE_GUEST, User::ROLE_EXPERIENCER, User::ROLE_HOST, User::ROLE_ADMIN)]
    public function getTrendingExperiences(
        ExperienceRepository $experienceRepository,
        SerializerInterface  $serializer,
    ): Response
    {
        $result = $experienceRepository->getTrendingExperiences();
        $data = $serializer->serialize(
            [
                'data' => $result,
                'message' => 'Experiences Successfully Retrieved',
                'status' => 'success',
            ],
            'json',
            ['groups' => 'experience']
        );

        return new Response(
            $data,
            Response::HTTP_OK,
            ['Content-type' => 'application/json'],
        );
    }
}
