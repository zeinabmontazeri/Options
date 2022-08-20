<?php

namespace App\Controller\Shop;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'app_shop_category',methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository)
    {
        $categoriesResponse = [
            'ok'=>True,
            'data' =>$categoryRepository->getAllCategories(),
            'message'=> 'success',
            'status' => 200,
        ];
        return new JsonResponse($categoriesResponse, 200);
    }
}
