<?php

namespace App\Controller\Admin;

use App\Auth\AcceptableRoles;
use App\Entity\Host;
use App\Entity\User;
use App\Repository\HostRepository;
use App\Request\AuthorizeAdminRequest;
use App\Request\HostAuthorizationFilterRequest;
use App\Service\Admin\HostApprovalService;
use App\Service\Admin\GetHostAuthorizationStatusByFilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/admin')]
class HostController extends AbstractController
{
    #[Route('/hosts/pending-approvals', name: 'app_admin_host_pending_approvals',methods: 'GET')]
//    #[AcceptableRoles(User::ROLE_ADMIN)]
    public function pendingApprovals(
        HostApprovalService $service): JsonResponse
    {

        $test = $service->getPendingList();
        return $this->json([
            'data' => $test,
            'message' => 'successfully got pending requests',
            'status' => 'success',
        ], Response::HTTP_OK);
    }

    #[Route('/hosts/{host_id}/authorize/', name: 'app_admin_host_authorization', requirements: ['id' => '\d+'], methods: 'POST')]
    #[ParamConverter('host', class: Host::class, options: ['id' => 'host_id'])]
    #[AcceptableRoles(User::ROLE_ADMIN)]
    public function authorizeHost(
        Host                        $host,
        HostRepository              $hostRepository,
        HostApprovalService $service,
        AuthorizeAdminRequest       $request): JsonResponse
    {

        $service->authorizeHost($host, $hostRepository, $request);
        return $this->json([
            'data' => [],
            'message' => 'successfully authorize host.',
            'status' => 'success',
        ], Response::HTTP_OK);
    }

    #[Route('/hosts/approval-status/', name: 'app_admin_host_approval_status', methods: 'GET')]
//    #[AcceptableRoles(User::ROLE_ADMIN)]
    public function filterHostByAuthorizationStatus(
        HostRepository                            $hostRepository,
        HostAuthorizationFilterRequest            $request,
        GetHostAuthorizationStatusByFilterService $service): JsonResponse
    {
        $result = $service->getHostByAuthorizationStatus($hostRepository, $request);
        return $this->json([
            'data' => $result,
            'message' => 'successfully get host by authorization status.',
            'status' => 'success',
        ], Response::HTTP_OK);

    }

}