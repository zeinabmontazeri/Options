<?php

namespace App\Service;

use App\DTO\ExperienceCollection;
use App\Entity\Experience;
use App\Entity\Host;
use App\Repository\CategoryRepository;
use App\Repository\ExperienceRepository;
use App\Request\ExperienceRequest;
use JetBrains\PhpStorm\ArrayShape;

class ExperienceService
{
    #[ArrayShape(['data' => "array", 'status' => "bool", 'message' => "string"])]
    public function getAll(ExperienceRepository $repository, Host $host): array
    {
        $res = [];
        $experiences = $repository->findBy(['host' => $host]);
        if (!$experiences) {
            $res['data'] = [];
        }
        foreach ($experiences as $experience) {
            $experienceCollection = new ExperienceCollection();
            $res['data'][] = $experienceCollection->toArray($experience);
        }
        $res['status'] = true;
        $res['message'] = 'Successfully retrieve all experience';
        return $res;
    }

    #[ArrayShape(['data' => "array", 'status' => "bool", 'message' => "string"])]
    public function create(ExperienceRepository $repository, ExperienceRequest $request, CategoryRepository $categoryRepository, Host $host): array
    {
        $res = ['data' => []];
        $experience = $repository->findBy(['title' => $request->title]);
        if (!$experience) {
            $experience = new Experience();
            $category = $categoryRepository->findOneBy(['name' => $request->category_name]);
            $experience->setCategory($category);
            $experience->setHost($host);
            $experience->setTitle($request->title);
            $experience->setDescription($request->description);
            $repository->add($experience, true);
            $res['data']['id'] = $experience->getId();
            $res['message'] = 'Experience successfully created';
            $res['status'] = true;
        } else {
            $res['message'] = 'Experience is duplicated';
            $res['status'] = false;
        }
        return $res;
    }

}
