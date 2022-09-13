<?php

namespace App\Controller\Auth;

use App\Auth\AcceptableRoles;
use App\Entity\User;
use App\Service\Admin\HostApprovalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/user/', name: 'auth.')]

class UserController extends AbstractController
{
    #[Route('upgrade', name: 'upgrade',methods:['post'])]
    #[AcceptableRoles(User::ROLE_EXPERIENCER)]
    public function index(HostApprovalService $approvalService): JsonResponse
    {
        $approvalService->addUpgradeRequest();

        return $this->json([
            'data' => null,
            'message' => 'Request has been created successfully',
            'status' => 'success',
        ]);
    }
}
