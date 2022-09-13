<?php

namespace App\Service;

use App\Auth\AuthenticatedUser;
use App\DTO\DtoFactory;
use App\Entity\Enums\EnumEventStatus;
use App\Entity\Experience;
use App\Entity\Host;
use App\Entity\Media;
use App\Repository\CategoryRepository;
use App\Repository\ExperienceRepository;
use App\Repository\MediaRepository;
use App\Request\ExperienceRequest;
use App\Request\ExperienceUpdateRequest;
use App\Request\ExperienceSearchRequest;
use App\Request\MediaRequest;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
        $experienceCollection = DtoFactory::getInstance();
        return $experienceCollection->toArray($experiences ,['host']);
    }

    public function getAllWithPagination(ExperienceRepository $repository, $perPage,$page): array
    {
        $host = $this->security->getUser()->getHost();
        return $repository->findByPaginated(['host'=>$host->getId()],null, $page , $perPage);
    }


    public function create(
        ExperienceRepository $repository,
        ExperienceRequest    $request,
        CategoryRepository   $categoryRepository): array
    {
        $res = ['data' => []];
        $host = $this->security->getUser()->getHost();
        $experience = $repository->findBy(['title' => $request->title]);
        if (!$experience) {
            $experience = new Experience();
            $category = $categoryRepository->findOneBy(['name' => $request->categoryName]);
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


    public function update(
        ExperienceRepository    $repository,
        ExperienceUpdateRequest $request,
        CategoryRepository      $categoryRepository,
        Experience              $experience): array
    {
        $res = ['data' => []];
        $host = $this->security->getUser()->getHost();
        if (isset($request->categoryName)) {
            $category = $categoryRepository->findOneBy(['name' => $request->categoryName]);
            if (!$category)
                throw new NotFoundHttpException("Category name does not exist.");
            $experience->setCategory($category);
        }
        if (isset($request->description))
            $experience->setDescription($request->description);
        $experience->setHost($host);
        if (isset($request->title))
            $experience->setTitle($request->title);

        $repository->add($experience, true);
        $res['message'] = 'Experience successfully updated';
        $res['status'] = 'success';
        return $res;
    }


    public function delete(
        ExperienceRepository $repository,
        Experience           $experience): array
    {
        $res = [];
        $allowToRemove = true;
        foreach ($experience->getEvents() as $event)
            if($event->getStatus() === EnumEventStatus::PUBLISHED)
                $allowToRemove = false;
        if(!$allowToRemove)
            throw new BadRequestHttpException('Provided experience has active event.');
        $repository->remove($experience , true);
        $res['message'] = 'Experience successfully deleted';
        $res['status'] = 'success';
        return $res;
    }

    public function addMedia(Experience $experience, MediaRepository $repository, MediaRequest $request)
    {
        if ($experience->getHost()->getUser() !== $this->security->getUser())
            throw new AccessDeniedException();
        $media = new Media();
        $media->setExperience($experience);
        $fileName = $media->uploadMedia($request->media);
        $media->setFileName($fileName);
        $repository->add($media, true);
    }

    public function search(ExperienceRepository $repository, ExperienceSearchRequest $request)
    {
        $searchedExperience = $repository->searchByWord($request->word);
        $experienceCollection = DtoFactory::getInstance('experienceFilter');
        return $experienceCollection->toArray($searchedExperience);
    }
}
