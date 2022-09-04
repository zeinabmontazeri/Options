<?php

namespace App\Controller\Host;

use App\Auth\AcceptableRoles;
use App\Entity\Host;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\ExperienceRepository;
use App\Request\ExperienceRequest;
use App\Service\ExperienceService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/hosts/')]
class ExperienceController extends AbstractController
{
    #[Route('{host_id}/experiences', name: 'app_host_experience' , methods: 'GET')]
    #[ParamConverter('host' , class: Host::class , options: ['id' => 'host_id'])]
    #[AcceptableRoles(User::ROLE_HOST , User::ROLE_ADMIN)]
    public function index(ExperienceService $service , ExperienceRepository $repository , Host $host): Response
    {
        return $this->json([
            'data' => $service->getAll($repository , $host),
            'status' => true,
            'message' => 'Successfully retrieve all experience'
        ]);
    }

    #[Route('{host_id}/experiences', name: 'app_host_experience_create' , methods: 'POST')]
    #[ParamConverter('host' , class: Host::class , options: ['id' => 'host_id'])]
    #[AcceptableRoles(User::ROLE_HOST)]
    public function create(ExperienceService $service , Host $host ,ExperienceRepository $repository , CategoryRepository $categoryRepository  , ExperienceRequest $request): Response
    {
        $res = $service->create($repository , $request ,  $categoryRepository , $host);
        return $this->json([
            'data' => $res['data'],
            'status' => $res['status'],
            'message' => $res['message']
        ]);
    }




}
