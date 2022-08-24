<?php

namespace App\Controller\Auth;

use App\Exception\ValidationException;
use App\Repository\UserRepository;
use App\Request\UserRegisterRequest;
use App\Service\UserRegisterService;
use Doctrine\ORM\EntityManagerInterface;
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
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $hasher
    ): Response
    {
        try {
            $validatedRequest = new  UserRegisterRequest($validator);
            $res = $userRegisterService->register($validatedRequest, $userRepository,$entityManager,$hasher);
            //TODO: We can generate token for user here
            return $this->json([
                'data' => [],
                'message' => 'User created successfully',
                'status' => 'success',
            ]);
        } catch (ValidationException $e) {
            return $this->json([
                'error'=>$e->getMessage(),
                'data'=>$e->getMessages(),
            ],Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->json([
                'error'=>$e->getMessage(),
                'data'=>null,
            ],Response::HTTP_BAD_REQUEST);
        }
        return $this->json(["a"=>50]);
    }
}
