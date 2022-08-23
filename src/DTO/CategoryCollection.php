<?php
namespace App\DTO;

use App\Entity\Category;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryCollection extends JsonResponse
{

    public function toArray(Category $category): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName()
        ];
    }
}