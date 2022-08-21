<?php

namespace App\Controller\Admin;

use App\DTO\CategoryCollection;
use App\Repository\CategoryRepository;
use App\Request\CategoryRequest;
use App\Service\CategoryService;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('api/admin/category', name: 'app_admin_category_get', methods: 'GET')]
    public function index(CategoryService $categoryService, CategoryRepository $repository): Response
    {
        $data = $categoryService->getAll($repository);
        return $this->json([
            'data' => $data,
            'message' => 'successfully retrieve all categories',
            'status' => true,
        ], 200);
    }

    #[Route('api/admin/category', name: 'app_admin_category_create', methods: 'POST')]
    public function create(CategoryService $categoryService, CategoryRepository $repository, CategoryRequest $request): Response
    {
        $res = $categoryService->create($repository, $request);
        return $this->json([
            'data' => [],
            'message' => $res['message'],
            'status' => $res['status'],
        ], 200);
    }

    #[Route('api/admin/category', name: 'app_admin_category_delete', methods: 'DELETE')]
    public function delete(CategoryService $categoryService, CategoryRepository $repository, CategoryRequest $request): Response
    {

        $res = $categoryService->delete($repository, $request->id);
        return $this->json([
            'data' => [],
            'message' => $res['message'],
            'status' => $res['status']
        ], 200);
    }

    #[Route('api/admin/category', name: 'app_admin_category_update', methods: 'PATCH')]
    public function update(CategoryService $categoryService, CategoryRepository $repository, CategoryRequest $request): Response
    {

        $res = $categoryService->update($repository, $request->id, $request->name);
        return $this->json([
            'data' => [],
            'message' => $res['message'],
            'status' => $res['status']
        ], 200);
    }


}
