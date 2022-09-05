<?php

namespace App\Controller\Admin;

use App\Auth\AcceptableRoles;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Request\CategoryRequest;
use App\Service\CategoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/admins')]
class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'app_admin_category_get', methods: 'GET')]
    #[AcceptableRoles(User::ROLE_ADMIN)]
    public function index(CategoryService $categoryService, CategoryRepository $repository): Response
    {
        return $this->json([
            'data' => $categoryService->getAll($repository),
            'message' => 'successfully retrieve all categories',
            'status' => 'success',
        ], Response::HTTP_OK);
    }

    #[Route('/categories', name: 'app_admin_category_create', methods: 'POST')]
    #[AcceptableRoles(User::ROLE_ADMIN)]
    public function create(
        CategoryService    $categoryService,
        CategoryRepository $repository,
        CategoryRequest    $request): Response
    {
        $res = $categoryService->create($repository, $request);
        return $this->json([
            'data' => [],
            'message' => $res['message'],
            'status' => $res['status'],
        ], Response::HTTP_OK);
    }

    #[Route('/categories/{category_id}', name: 'app_admin_category_delete', methods: 'DELETE')]
    #[ParamConverter('category', class: Category::class, options: ['id' => 'category_id'])]
    #[AcceptableRoles(User::ROLE_ADMIN)]
    public function delete(
        Category           $category,
        CategoryService    $categoryService,
        CategoryRepository $repository): Response
    {
        $res = $categoryService->delete($repository, $category);
        return $this->json([
            'data' => [],
            'message' => $res['message'],
            'status' => $res['status']
        ], Response::HTTP_OK);
    }

    #[Route('/categories/{category_id}', name: 'app_admin_category_update', methods: 'PATCH')]
    #[ParamConverter('category', class: Category::class, options: ['id' => 'category_id'])]
    #[AcceptableRoles(User::ROLE_ADMIN)]
    public function update(
        CategoryService    $categoryService,
        CategoryRepository $repository,
        Category           $category,
        CategoryRequest    $request): Response
    {

        $res = $categoryService->update($repository, $category, $request->name);
        return $this->json([
            'data' => [],
            'message' => $res['message'],
            'status' => $res['status']
        ], Response::HTTP_OK);
    }


}
