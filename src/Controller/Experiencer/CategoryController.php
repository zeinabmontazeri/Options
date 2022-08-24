<?php

namespace App\Controller\Experiencer;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
#[Route('api/experiencer')]
class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'app_experiencer_category',methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository)
    {
        $categoriesResponse = [
            'ok'=>True,
            'data' =>$categoryRepository->findAll(),
            'message'=> 'success',
            'status' => 200,
        ];
        return new JsonResponse($categoriesResponse, 200);
    }
}
