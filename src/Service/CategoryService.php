<?php

namespace App\Service;

use App\DTO\CategoryCollection;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Request\CategoryRequest;
use Monolog\DateTimeImmutable;
use Symfony\Bridge\Doctrine\ManagerRegistry;


class CategoryService
{

    public function getAll(CategoryRepository $repository): array
    {
        $categories = $repository->findAll();
        $res = [];
        foreach ($categories as $category) {
            $categoryCollection = new CategoryCollection();
            $categoryCollection->name = $category->getName();
            $categoryCollection->id = $category->getId();
            $res[] = $categoryCollection;
        }
        return $res;
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
            $res['message'] = 'category successfully created';
            $res['status'] = true;
        } else {
            $res['message'] = 'category is duplicated';
            $res['status'] = false;
        }
        return $res;

    }

    public function delete(CategoryRepository $repository, $id): array
    {
        $res = [];
        $category = $repository->find($id);
        if (!$category) {
            $res['message'] = 'category id is invalid';
            $res['status'] = false;
        } else {
            $repository->remove($category, true);
            $res['message'] = 'category successfully deleted';
            $res['status'] = true;
        }
        return $res;
    }

    public function update(CategoryRepository $repository, $id, $name): array
    {
        $res = [];
        $category = $repository->find($id);
        if (!$category) {
            $res['message'] = 'No product found';
            $res['status'] = false;
        } else {
            $category->setName($name);
            $repository->add($category, true);
            $res['message'] = 'updated successfully';
            $res['status'] = true;
        }
        return $res;
    }
}