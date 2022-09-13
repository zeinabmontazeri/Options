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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route('api/v1/admin')]
/**
 * @OA\Tag(name="Admin")
 */
class HostController extends AbstractController
{
    /**
     * List of pending hosts
     *
     * Get List of pending host approval requests
     * @OA\Get(
     *     @OA\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          description="The page of list",
     *          @OA\Schema(
     *              type="integer",
     *              example=3
     *          ),
     *     ),
     *     @OA\Parameter(
     *          name="perpage",
     *          in="query",
     *          required=false,
     *          description="The item count per page",
     *          @OA\Schema(
     *              type="integer",
     *              example=2
     *          ),
     *     ),
     * )
     * @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="boolean",
     *                         description="for 200 is success"
     *                     ),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         @OA\Property(
     *                               property="results",
     *                               type="array",
     *                               description="The list of requests",
     *                              @OA\Items(
     *                                  @OA\Property(
     *                                      property="id",
     *                                      type="integer",
     *                                      example="2"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="message",
     *                                      type="string",
     *                                      example="Wants to be host as new user"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="status",
     *                                      type="string",
     *                                      example="PENDING"
     *                                  ),
     *                                  @OA\Property(
     *                                      property="createdAt",
     *                                      type="date",
     *                                      example="2022-09-13T00:56:01+00:00"
     *                                  ),
     *                              ),
     *                          ),
     *                         @OA\Property(
     *                               property="current_page",
     *                               type="integer",
     *                               description="The current page number",
     *                          ),
     *                         @OA\Property(
     *                               property="per_page",
     *                               type="integer",
     *                               description="Count of item per page",
     *                          ),
     *                         @OA\Property(
     *                               property="total",
     *                               type="integer",
     *                               description="Total count of items",
     *                          ),
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="message",
     *                         description="The action message",
     *                     ),
     *                     example={
     *                             "status": "success",
     *                             "data": {
     *                               "results": {
     *                                  {
     *                                      "id": 5,
     *                                      "message": "Wants to be host as new user",
     *                                      "status": "PENDING",
     *                                      "createdAt": "2022-09-13T00:56:01+00:00"
     *                                  },
     *                                  {
     *                                      "id": 6,
     *                                      "message": "Wants to be host as new user",
     *                                      "status": "PENDING",
     *                                      "createdAt": "2022-09-13T00:56:01+00:00"
     *                                  }
     *                                },
     *                               "current_page": 3,
     *                               "per_page": 2,
     *                               "total": 11
     *                              },
     *                             "message": "successfully got pending requests"
     *                     }
     *                 )
     *             )
     *         }
     *     )
     *
     */
    #[Route('/hosts/pending-approvals', name: 'app_admin_host_pending_approvals',methods: 'GET')]
    #[AcceptableRoles(User::ROLE_ADMIN)]
    public function pendingApprovals(
        Request $request,
        HostApprovalService $service): JsonResponse
    {
        $perPage = intval($request->query->get('perpage', 10));
        $page = intval($request->query->get('page', 1));
        $test = $service->getPendingList($page,$perPage);
        return $this->json([
            'status' => 'success',
            'data' => $test,
            'message' => 'successfully got pending requests',
        ], Response::HTTP_OK);
    }

    /**
     * Set result of approval request
     *
     * Approve or reject a request, send `REJECTED` for reject and `ACCEPTED` for accept
     * @OA\RequestBody(
     *    required=true,
     *    description="Provide All Data Below",
     *    @OA\JsonContent(
     *       required={"request_id","status"},
     *       @OA\Property(property="request_id", type="integer", format="integer", example="2"),
     *       @OA\Property(property="status", type="enum", format="text", example="REJECTED"),
     *    ),
     * )
     * @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="string",
     *                         description="for 200 is success"
     *                     ),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="message",
     *                         description="The action message",
     *                     ),
     *                     example={
     *                             "status": "success",
     *                             "data": null,
     *                             "message": "The requested action has been applied successfully"
     *                     }
     *                 )
     *             )
     *         }
     *     )
     *
     */
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