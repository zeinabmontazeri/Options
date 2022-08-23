<?php
namespace App\Controller\Admin;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Request\CategoryRequest;
use App\Service\CategoryService;
use PHPUnit\Util\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/admin')]
class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_admin_category_get', methods: 'GET')]
    public function index(CategoryService $categoryService, CategoryRepository $repository): Response
    {
        $data = $categoryService->getAll($repository);
        return $this->json([
            'data' => $data,
            'message' => 'successfully retrieve all categories',
            'status' => true,
        ], Response::HTTP_OK);
    }

    #[Route('/category', name: 'app_admin_category_create', methods: 'POST')]
    public function create(CategoryService $categoryService, CategoryRepository $repository, CategoryRequest $request): Response
    {
        try {
            $res = $categoryService->create($repository, $request);
        }catch(Exception $exception)
        {
            return $this->json([
                'data' => [],
                'message' => $exception->getMessage(),
                'status' => false,
            ], $exception->getCode());
        }
        return $this->json([
            'data' => [],
            'message' => $res['message'],
            'status' => $res['status'],
        ], Response::HTTP_OK);
    }

    #[Route('/category/{category_id}', name: 'app_admin_category_delete', methods: 'DELETE')]
    #[ParamConverter('category' , class: Category::class , options: ['id' => 'category_id'])]
    public function delete(Category $category , CategoryService $categoryService, CategoryRepository $repository): Response
    {

        $res = $categoryService->delete($repository, $category );
        return $this->json([
            'data' => [],
            'message' => $res['message'],
            'status' => $res['status']
        ], Response::HTTP_OK);
    }

    #[Route('/category/{category_id}', name: 'app_admin_category_update', methods: 'PATCH')]
    #[ParamConverter('category' , class: Category::class , options: ['id' => 'category_id'])]
    public function update(CategoryService $categoryService, CategoryRepository $repository, Category $category, CategoryRequest $request): Response
    {

        $res = $categoryService->update($repository, $category, $request->name);
        return $this->json([
            'data' => [],
            'message' => $res['message'],
            'status' => $res['status']
        ], Response::HTTP_OK);
    }


}
