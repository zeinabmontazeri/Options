<?php

namespace App\Controller\Admin;

use App\Auth\AcceptableRoles;
use App\Entity\Host;
use App\Entity\User;
use App\Service\HostService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/admins')]
class HostController extends AbstractController
{
    #[Route('/hosts/{host_id}/commissions', name: 'app_admin_get_host_commissions', methods: 'GET')]
    #[ParamConverter('host',class:Host::class,options: ['id'=>'host_id'])]
    #[AcceptableRoles(User::ROLE_ADMIN)]
    public function index(Host $host,HostService $hostService): Response
    {
        return $this->json([
            'data' => $hostService->getTotalCommissions($host),
            'message' => 'successfully calculated total commission',
            'status' => 'success',
        ], Response::HTTP_OK);
    }

}
