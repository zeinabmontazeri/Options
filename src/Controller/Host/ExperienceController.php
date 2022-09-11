<?php

namespace App\Controller\Host;

use App\Auth\AcceptableRoles;
use App\Entity\Experience;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\ExperienceRepository;
use App\Request\ExperienceRequest;
use App\Service\ExperienceService;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/host')]
class ExperienceController extends AbstractController
{
    #[Route('/experiences', name: 'app_host_experience', methods: 'GET')]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function index(ExperienceService $service, ExperienceRepository $repository): Response
    {
        return $this->json([
            'data' => $service->getAll($repository),
            'status' => 'success',
            'message' => 'Successfully retrieve all experience'
        ]);
    }

    #[Route('/experiences', name: 'app_host_experience_create', methods: 'POST')]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function create(
        ExperienceService    $service,
        ExperienceRepository $repository,
        CategoryRepository   $categoryRepository,
        ExperienceRequest    $request): Response
    {
        $res = $service->create($repository, $request, $categoryRepository);
        return $this->json([
            'data' => $res['data'],
            'status' => $res['status'],
            'message' => $res['message']
        ]);
    }

    #[Route('/experiences/{experience_id}', name: 'app_host_experience_update', methods: 'PATCH')]
    #[AcceptableRoles(User::ROLE_HOST)]
    #[ParamConverter(data: 'experience' , class: Experience::class ,options: ['id' => 'experience_id'])]
    public function update(
        ExperienceService    $service,
        ExperienceRepository $repository,
        CategoryRepository   $categoryRepository,
        ExperienceRequest    $request,
        Experience           $experience): Response
    {
        $res = $service->update($repository, $request, $categoryRepository , $experience);
        return $this->json([
            'data' => [],
            'status' => $res['status'],
            'message' => $res['message']
        ]);
    }


    #[Route('/experiences/{experience_id}', name: 'app_host_experience_delete', methods: 'DELETE')]
    #[AcceptableRoles(User::ROLE_HOST)]
    #[ParamConverter(data: 'experience' , class: Experience::class ,options: ['id' => 'experience_id'])]
    public function delete(
        ExperienceService    $service,
        ExperienceRepository $repository,
        Experience           $experience): Response
    {
        $res = $service->delete($repository , $experience);
        return $this->json([
            'data' => [],
            'status' => $res['status'],
            'message' => $res['message']
        ]);
    }


}
