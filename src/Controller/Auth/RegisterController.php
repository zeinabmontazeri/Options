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
     * @OA\Tag(name="User")
     * @OA\RequestBody(
     *    required=true,
     *    description="Provide All Data Below",
     *    @OA\JsonContent(
     *       required={"phoneNumber","birthDate","password","gender","role","firstName","lastName"},
     *       @OA\Property(property="phoneNumber", type="phone", format="text", example="09123456789"),
     *       @OA\Property(property="birthDate", type="datetime", format="text", example="1993-06-03 12:09:50"),
     *       @OA\Property(property="password", type="string", format="text", example="sample@password123"),
     *       @OA\Property(property="gender", type="ENUM", format="text", example="FEMALE"),
     *       @OA\Property(property="role", type="ENUM", format="text", example="ROLE_HOST"),
     *       @OA\Property(property="firstName", type="string", format="text", example="مهرداد"),
     *       @OA\Property(property="lastName", type="string", format="text", example="محمدی"),
     *    ),
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
