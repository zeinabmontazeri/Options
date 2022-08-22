<?php

namespace App\Controller\Auth;

use App\Exception\ValidationException;
use App\Repository\UserRepository;
use App\Request\UserRegisterRequest;
use App\Service\UserRegisterService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/v1/auth/', name: 'auth.')]
class RegisterController extends AbstractController
{
    #[Route('register', name: 'register',methods:['post'])]
    public function index(ValidatorInterface $validator,
    UserRegisterService $userRegisterService,
    UserRepository $userRepository,
    UserPasswordHasherInterface $hasher
    ): Response
    {
        try {
            $validatedRequest = new  UserRegisterRequest($validator);
            $res = $userRegisterService->register($validatedRequest, $userRepository,$hasher);
            //TODO: We can generate token for user here
            return $this->json([
                'data' => [],
                'message' => 'User created successfully',
                'status' => 'success',
            ], 200);
        } catch (ValidationException $e) {
            return $this->json([
                'error'=>$e->getMessage(),
                'data'=>$e->getMessages(),
            ],400);
        } catch (Exception $e) {
            return $this->json([
                'error'=>$e->getMessage(),
                'data'=>null,
            ],400);
        }
        return $this->json(["a"=>50]);
    }
}
