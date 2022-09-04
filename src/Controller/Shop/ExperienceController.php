<?php

namespace App\Controller\Shop;

use App\Auth\AcceptableRoles;
use App\DTO\DtoFactory;
use App\Entity\Experience;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;
use App\Request\ExperienceFilterRequest;
use App\Service\Shop\GetAllExperienceEventsService;
use App\Service\Shop\GetExperiencesByFilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/shop')]
class ExperienceController extends AbstractController
{
    #[Route('/experiences', name: 'app_get_experiences', methods: ['GET'])]
    #[AcceptableRoles(User::ROLE_GUEST, User::ROLE_EXPERIENCER, User::ROLE_ADMIN, User::ROLE_HOST)]
    public function filterExperiences(
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
                'status' => 'success',
            ], Response::HTTP_OK
        );
    }

    #[Route('/experiences/{experience_id}/events/', name: 'app_experience_event_list', methods: ['GET'])]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    #[AcceptableRoles(User::ROLE_GUEST, User::ROLE_EXPERIENCER, User::ROLE_ADMIN, User::ROLE_HOST)]
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

    #[Route('/experiences/trending/', name: 'app_trending_experience', methods: ['GET'])]
    #[AcceptableRoles(User::ROLE_GUEST, User::ROLE_EXPERIENCER, User::ROLE_HOST, User::ROLE_ADMIN)]
    public function getTrendingExperiences(
        ExperienceRepository $experienceRepository,
    ): JsonResponse
    {
        $result = $experienceRepository->getTrendingExperiences();
        return $this->json(
            [
                'data' => $result,
                'message' => 'Experiences Successfully Retrieved',
                'status' => 'success',
            ], Response::HTTP_OK
        );
    }

    #[Route('/experiences/search/{word}', name: 'app_experience_search', methods: ['GET'])]
    #[AcceptableRoles(User::ROLE_GUEST, User::ROLE_EXPERIENCER, User::ROLE_ADMIN, User::ROLE_HOST)]
    public function searchExperience($word, ExperienceRepository $experienceRepository)
    {
        $searchResult = $experienceRepository->searchByWord($word);
        $experienceCollection = DtoFactory::getInstance('experienceFilter');
        $experiences = $experienceCollection->toArray($searchResult);
        return $this->json(
            [
                'data' => $experiences,
                'message' => 'Experiences Successfully Retrieved',
                'status' => 'success',
            ], Response::HTTP_OK
        );
    }
}