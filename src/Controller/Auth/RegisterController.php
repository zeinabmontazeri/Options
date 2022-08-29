<?php

namespace App\Controller\Auth;

use App\Request\UserRegisterRequest;
use App\Service\UserRegisterService;
use OpenApi\Annotations\JsonContent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use OpenApi\Attributes as OA2;

#[Route('api/v1/auth/', name: 'auth.')]

class RegisterController extends AbstractController
{
    /**
     * Register new user
     *
     * Creates new user if all fields are valid

     *  @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         example={
     *             "status": "status",
     *             "status2": "status",
     *             "comment": "comment"
     *         },
     *         @OA\Schema (
     *              type="object",
     *              @OA\Parameter(parameter="status", required=true, description="Event Status"),
     *              @OA\Property(propertyNames="asdasd",property="status", required=true, description="Event Status", type="string"),
     *              @OA\Property(property="comment", required=false, description="Change Status Comment", type="string"),
     *         )
     *     )
     * )
     */
    #[Route('register', name: 'register',methods:['post'])]
//    #[OA2\Response(
//        response:400,
//        description:"Bad Request",
//        content: new OA2\JsonContent(
//            ref:
//        )
//    )]
    public function index(
        UserRegisterRequest $validatedRequest,
        UserRegisterService $userRegisterService
    ): JsonResponse
    {
            $userRegisterService->register($validatedRequest);
            //TODO: We can generate token for user here
            return $this->json([
                'success' => true,
                'data' => null,
                'message' => 'User has been created successfully',
            ]);
    }
}
