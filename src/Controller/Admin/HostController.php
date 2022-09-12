<?php

namespace App\Controller\Admin;

use App\Auth\AcceptableRoles;
use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Host;
use App\Entity\UpgradeRequest;
use App\Entity\User;
use App\Repository\HostRepository;
use App\Repository\UpgradeRequestRepository;
use App\Request\AuthorizeAdminRequest;
use App\Request\HostApproveRequest;
use App\Request\HostAuthorizationFilterRequest;
use App\Service\Admin\HostApprovalService;
use App\Service\Admin\GetHostAuthorizationStatusByFilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/admin')]
class HostController extends AbstractController
{
    #[Route('/hosts/pending-approvals', name: 'app_admin_host_pending_approvals',methods: 'GET')]
    #[AcceptableRoles(User::ROLE_ADMIN)]
    public function pendingApprovals(
        HostApprovalService $service): JsonResponse
    {

        $test = $service->getPendingList($page=1,$perPage = 20);
        return $this->json([
            'data' => $test,
            'message' => 'successfully got pending requests',
            'status' => 'success',
        ], Response::HTTP_OK);
    }

    #[Route('/hosts/approve', name: 'app_admin_host_approve_host',methods: 'POST')]
    #[AcceptableRoles(User::ROLE_ADMIN)]
    public function approveHost(
        HostApproveRequest $approveRequest,
        UpgradeRequestRepository $repository,
        HostApprovalService $service): JsonResponse
    {
        $upgradeRequest = $repository->find($approveRequest->request_id);
        if(!$upgradeRequest) throw new BadRequestHttpException('The Requested resource not found');
        if($upgradeRequest->getStatus() != EnumPermissionStatus::PENDING) throw new BadRequestHttpException('The request already applied');

        $service->changeUpgradeStatus($upgradeRequest,EnumPermissionStatus::from($approveRequest->status));

        return $this->json([
            'data' => null,
            'message' => 'The requested action has been applied successfully',
            'status' => 'success',
        ], Response::HTTP_OK);
    }
}