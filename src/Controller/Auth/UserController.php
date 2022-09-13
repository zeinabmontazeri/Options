<?php

namespace App\Controller\Auth;

use App\Auth\AcceptableRoles;
use App\Entity\User;
use App\Service\Admin\HostApprovalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route('api/v1/user/', name: 'auth.')]

class UserController extends AbstractController
{
    /**
     * Upgrade plan
     *
     * Request to be upgrade plan and became a host
     * @OA\Tag(name="Experiencer")
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
     *                             "message": "Request has been created successfully"
     *                     }
     *                 )
     *             )
     *         }
     *     )
     * @OA\Response(
     *         response="400",
     *         description="fail",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="status",
     *                         type="string"
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
     *                             "status": "fail",
     *                             "data": null,
     *                             "message": "You already have a pending request for upgrade"
     *                     }
     *                 )
     *             )
     *         }
     *     )
     *
     */
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
