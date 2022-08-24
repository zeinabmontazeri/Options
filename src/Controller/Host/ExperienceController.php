<?php

namespace App\Controller\Host;

use App\Entity\Host;
use App\Repository\CategoryRepository;
use App\Repository\ExperienceRepository;
use App\Repository\HostRepository;
use App\Request\ExperienceRequest;
use App\Service\ExperienceService;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/host/')]
class ExperienceController extends AbstractController
{
    #[Route('{host_id}/experience', name: 'app_host_experience' , methods: 'GET')]
    #[ParamConverter('host' , class: Host::class , options: ['id' => 'host_id'])]
    public function index(ExperienceService $service , ExperienceRepository $repository , Host $host): Response
    {
        $res = $service->getAll($repository , $host);
        return $this->json([
            'data' => $res['data'],
            'status' => $res['status'],
            'message' => $res['message']
        ]);
    }

    #[Route('{host_id}/experience', name: 'app_host_experience_create' , methods: 'POST')]
    #[ParamConverter('host' , class: Host::class , options: ['id' => 'host_id'])]
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
