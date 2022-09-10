<?php

namespace App\Service;

use App\Auth\AuthenticatedUser;
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
    public function __construct(private AuthenticatedUser $security)
    {
        $this->security = $security;
    }

    public function getAll(ExperienceRepository $repository): array
    {
        $host = $this->security->getUser()->getHost();
        $experiences = $repository->findBy(['host' => $host]);
        $experienceCollection = DtoFactory::getInstance('experience');
        return $experienceCollection->toArray($experiences);
    }

    public function getAllWithPagination(ExperienceRepository $repository, $perPage,$page): array
    {
        $host = $this->security->getUser()->getHost();
        return $repository->findByPaginated(['host'=>$host->getId()],null, $page , $perPage);
    }


    public function create(ExperienceRepository $repository, ExperienceRequest $request, CategoryRepository $categoryRepository): array
    {
        $res = ['data' => []];
        $host = $this->security->getUser()->getHost();
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
            $res['status'] = 'success';
        } else {
            throw new BadRequestException("Experience title should be unique , you have already this title name. ", 400);
        }
        return $res;
    }

}
