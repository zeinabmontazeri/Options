<?php

namespace App\Controller\Host;

use App\Auth\AcceptableRoles;
use App\Entity\Experience;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\ExperienceRepository;
use App\Repository\MediaRepository;
use App\Request\ExperienceRequest;
use App\Request\ExperienceUpdateRequest;
use App\Request\MediaRequest;
use App\Service\ExperienceService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('api/v1/host')]
class ExperienceController extends AbstractController
{
    #[Route('/experiences', name: 'app_host_experience', methods: 'GET')]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function index(
        ExperienceService    $service,
        ExperienceRepository $repository,
        Request              $request
    ): Response
    {
        $perPage = intval($request->query->get('perpage', 10));
        $page = intval($request->query->get('page', 1));
        $data = $service->getAllWithPagination($repository, $perPage, $page);
        $prev_page = $page <= 1 ? 1 : $page - 1;
        $last_page = ceil($data['total'] / $perPage);
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $data['next_page'] = $this->generateUrl('app_host_experience',
            array('page' => $next_page, 'perpage' => $perPage), UrlGeneratorInterface::ABSOLUTE_URL);
        $data['prev_page'] = $this->generateUrl('app_host_experience',
            array('page' => $prev_page, 'perpage' => $perPage), UrlGeneratorInterface::ABSOLUTE_URL);
        $data['first_page'] = $this->generateUrl('app_host_experience',
            array('page' => 1, 'perpage' => $perPage), UrlGeneratorInterface::ABSOLUTE_URL);
        $data['last_page'] = $this->generateUrl('app_host_experience',
            array('page' => $last_page, 'perpage' => $perPage), UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->json([
            'data' => $data,
            'status' => true,
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
    #[ParamConverter(data: 'experience', class: Experience::class, options: ['id' => 'experience_id'])]
    public function update(
        ExperienceService       $service,
        ExperienceRepository    $repository,
        CategoryRepository      $categoryRepository,
        ExperienceUpdateRequest $request,
        Experience              $experience): Response
    {
        $res = $service->update($repository, $request, $categoryRepository, $experience);
        return $this->json([
            'data' => [],
            'status' => $res['status'],
            'message' => $res['message']
        ]);
    }


    #[Route('/experiences/{experience_id}', name: 'app_host_experience_delete', methods: 'DELETE')]
    #[AcceptableRoles(User::ROLE_HOST)]
    #[ParamConverter(data: 'experience', class: Experience::class, options: ['id' => 'experience_id'])]
    public function delete(
        ExperienceService    $service,
        ExperienceRepository $repository,
        Experience           $experience): Response
    {
        $res = $service->delete($repository, $experience);
        return $this->json([
            'data' => [],
            'status' => $res['status'],
            'message' => $res['message']
        ]);
    }

    #[Route('/experiences/{experience_id}/add-media', name: 'app_host_experience_add_media', methods: 'POST')]
    #[ParamConverter('experience', class: Experience::class, options: ['id' => 'experience_id'])]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function addImage(Experience $experience, MediaRequest $request, ExperienceService $service, MediaRepository $repository)
    {
        $service->addMedia($experience, $repository, $request);
        return $this->json([
            'data' => [],
            'message' => 'media added successfully',
            'status' => 'success'
        ]);
    }
}
