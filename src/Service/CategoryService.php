<?php
namespace App\Service;

use App\DTO\CategoryCollection;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Request\CategoryRequest;
use PHPUnit\Util\Exception;
use Symfony\Component\HttpFoundation\Response;

class CategoryService
{

    public function getAll(CategoryRepository $repository): array
    {
        $categories = $repository->findAll();
        $res = [];
        if (!$categories) {
            $res['data'] = [];
        }
        foreach ($categories as $category) {
            $categoryCollection = new CategoryCollection();
            $res[] = $categoryCollection->toArray($category);
        }
        return $res;
    }

    public function create(CategoryRepository $repository, CategoryRequest $request): array
    {
        $res = [];
        if ($request->errors) {
            throw new Exception($request->errors , 400);
        }
        $category = $repository->findBy(['name' => $request->name]);
        if (!$category) {
            $category = new Category();
            $category->setName($request->name);
            $category->setCreatedAt();
            $repository->add($category, true);
            $res['message'] = 'category successfully created';
            $res['status'] = true;
        } else {
            $res['message'] = 'category is duplicated';
            $res['status'] = false;
            $res['httpStatus'] = Response::HTTP_OK;
        }
        return $res;

    }

    public function delete(CategoryRepository $repository, Category $category): array
    {
        $res = [];
        $repository->remove($category, true);
        $res['message'] = 'category successfully deleted';
        $res['status'] = true;

        return $res;
    }

    public function update(CategoryRepository $repository, Category $category, $name): array
    {
        $res = [];
            $category->setName($name);
            $repository->add($category, true);
            $res['message'] = 'updated successfully';
            $res['status'] = true;

        return $res;
    }
}