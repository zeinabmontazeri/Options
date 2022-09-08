<?php

namespace App\Controller\Host;

use App\Auth\AcceptableRoles;
use App\Entity\Host;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\ExperienceRepository;
use App\Request\ExperienceRequest;
use App\Service\ExperienceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('api/v1/hosts', name: 'app_host_')]
class ExperienceController extends AbstractController
{
    #[Route('/experiences', name: 'experience', methods: 'GET')]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function index(ExperienceService    $service,
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
            array('page' => $next_page, 'perpage' => $perPage),UrlGeneratorInterface::ABSOLUTE_URL);
        $data['prev_page'] = $this->generateUrl('app_host_experience',
            array('page' => $prev_page, 'perpage' => $perPage),UrlGeneratorInterface::ABSOLUTE_URL);
        $data['first_page'] = $this->generateUrl('app_host_experience',
            array('page' => 1, 'perpage' => $perPage),UrlGeneratorInterface::ABSOLUTE_URL);
        $data['last_page'] = $this->generateUrl('app_host_experience',
            array('page' => $last_page, 'perpage' => $perPage),UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->json([
            'data' => $data,
            'status' => true,
            'message' => 'Successfully retrieve all experience'
        ]);
    }

    #[Route('/experiences', name: 'experience_create', methods: 'POST')]
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


}
