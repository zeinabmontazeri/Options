<?php

namespace App\Service;

use App\DTO\DtoFactory;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Request\CategoryRequest;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CategoryService
{

    public function getAll(CategoryRepository $repository): array
    {
        $categories = $repository->findAll();
        $categoryCollection = DtoFactory::getInstance(Category::class);
        return $categoryCollection->toArray($categories);
    }

    public function create(CategoryRepository $repository, CategoryRequest $request): array
    {
        $res = [];
        $category = $repository->findBy(['name' => $request->name]);
        if (!$category) {
            $category = new Category();
            $category->setName($request->name);
            $category->setCreatedAt();
            $repository->add($category, true);
            $res['data']['id'] = $category->getId();
            $res['message'] = 'category successfully created';
            $res['status'] = 'success';
        } else {
            throw new BadRequestException("Name should be unique , you have already this name. ", 400);
        }
        return $res;

    }

    public function delete(CategoryRepository $repository, Category $category): array
    {
        $res = [];
        $repository->remove($category, true);
        $res['message'] = 'category successfully deleted';
        $res['status'] = 'success';

        return $res;
    }

    public function update(CategoryRepository $repository, Category $category, $name): array
    {
        $res = [];
        $category->setName($name);
        $repository->add($category, true);
        $res['message'] = 'updated successfully';
        $res['status'] = 'success';

        return $res;
    }
}