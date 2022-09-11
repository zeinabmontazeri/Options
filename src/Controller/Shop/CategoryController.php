<?php

namespace App\Controller\Shop;

use App\Auth\AcceptableRoles;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1')]
class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'app_shop_categories', methods: ['GET'])]
    #[AcceptableRoles(User::ROLE_HOST, User::ROLE_EXPERIENCER, User::ROLE_ADMIN, User::ROLE_GUEST)]
    public function index(CategoryService $categoryService, CategoryRepository $repository): Response
    {
        $data = $categoryService->getAll($repository);
        return $this->json([
            'data' => $data,
            'message' => 'successfully retrieve all categories',
            'status' => 'success',
        ], Response::HTTP_OK);
    }
}
