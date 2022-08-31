<?php

namespace App\Service;

use App\DTO\DtoFactory;
use App\Entity\Experience;
use App\Entity\Host;
use App\Repository\CategoryRepository;
use App\Repository\ExperienceRepository;
use App\Request\ExperienceRequest;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExperienceService
{

    public function getAll(ExperienceRepository $repository, Host $host): array
    {
        $experiences = $repository->findBy(['host' => $host]);
        $experienceCollection = DtoFactory::getInstance('experience');
        return $experienceCollection->toArray($experiences);
    }

    public function getAllWithPagination(ExperienceRepository $repository, Host $host,$perPage,$page): array
    {
        return $repository->findByPaginated([],null, $page , $perPage);
    }


    public function create(ExperienceRepository $repository, ExperienceRequest $request, CategoryRepository $categoryRepository, Host $host): array
    {
        $res = ['data' => []];
        $experience = $repository->findBy(['title' => $request->title]);
        if (!$experience) {
            $experience = new Experience();
            $category = $categoryRepository->findOneBy(['name' => $request->category_name]);
            if (!$category)
                throw new NotFoundHttpException("Category name does not exist.");
            $experience->setCategory($category);
            $experience->setHost($host);
            $experience->setTitle($request->title);
            $experience->setDescription($request->description);
            $repository->add($experience, true);
            $res['data']['id'] = $experience->getId();
            $res['message'] = 'Experience successfully created';
            $res['status'] = true;
        } else {
            throw new BadRequestException("Experience title should be unique , you have already this title name. ", 400);
        }
        return $res;
    }

}
