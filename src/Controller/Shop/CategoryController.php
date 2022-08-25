<?php

namespace App\Controller\Shop;

use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('api/shop')]
class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'app_shop_categories')]
    public function index(CategoryService $categoryService, CategoryRepository $repository): Response
    {
        $data = $categoryService->getAll($repository);
        return $this->json([
            'data' => $data,
            'message' => 'successfully retrieve all categories',
            'status' => true,
        ], Response::HTTP_OK);
    }
}
