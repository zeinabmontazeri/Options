<?php

namespace App\Controller\Auth;

use App\Request\UserRegisterRequest;
use App\Service\UserRegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/auth/', name: 'auth.')]
class RegisterController extends AbstractController
{
    #[Route('register', name: 'register', methods: ['post'])]
    public function index(
        UserRegisterRequest $validatedRequest,
        UserRegisterService $userRegisterService
    ): JsonResponse
    {
        $userRegisterService->register($validatedRequest);
        //TODO: We can generate token for user here
        return $this->json([
            'data' => null,
            'message' => 'User has been created successfully',
            'status' => 'success',
        ]);
    }
}
