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

    public function getAll(CategoryRepository $repository ) : array
    {
        $categories =  $repository->findAll();
        $out = [];
        foreach ($categories as $category)
        {
            $categoryCollection = new CategoryCollection();
            $categoryCollection->name = $category->getName();
            $categoryCollection->id = $category->getId();
            $out[] = $categoryCollection;
        }
        return $out;
    }

    public function create(CategoryRepository $repository , CategoryRequest $request) :string
    {
        $category = $repository->findBy(['name' => $request->name]);
        if(!$category) {
            $category = new Category();
            $category->setName($request->name);
            $category->setCreatedAt();
            $repository->add($category, true);
            return "category successfully created";
        }
        else return "category duplicated";

    }

    public function delete(CategoryRepository $repository ,$id):string
    {
        $category = $repository->find($id);
        if(!$category)
        {
            return "category id is invalid";
        }
        else {
            $repository->remove($category, true);
            return "category successfully deleted";
        }
    }

    public function update(CategoryRepository $repository ,$id , $name):string
    {
        //$repository->update($id , $name);

        $category = $repository->find($id);
        if (!$category) {
           return "No product found";
        }
        $category->setName($name);
        $repository->add($category, true);
        return "updated successfully";
    }
}