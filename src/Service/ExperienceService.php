<?php

namespace App\Service;

use App\DTO\DtoFactory;
use App\Entity\Enums\EnumEventStatus;
use App\Entity\Experience;
use App\Entity\Host;
use App\Repository\CategoryRepository;
use App\Repository\ExperienceRepository;
use App\Repository\OrderRepository;
use App\Request\ExperienceRequest;
use App\Request\ExperienceStatusUpdateRequest;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class ExperienceService
{

    public function __construct(private OrderRepository $orderRepository,
                                private Security $security)
    {
    }

    public function getAll(ExperienceRepository $repository, Host $host): array
    {
        $experiences = $repository->findBy(['host' => $host]);
        $experienceCollection = DtoFactory::getInstance('experience');
        return $experienceCollection->toArray($experiences);
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

    public function changeStatus(Experience $experience, ExperienceStatusUpdateRequest $request): bool
    {
        if ($this->security->getUser() !== $experience->getHost()->getUser())
            throw new AccessDeniedException();

        $newStatus = EnumEventStatus::from($request->status);
        //Check if status have been changed
        if($newStatus != $experience->getStatus()) {
            //Check if we can change status for this event
            if($newStatus != EnumEventStatus::PUBLISHED){
                $completedOrders = $this->orderRepository->getCompletedOrdersByExperience($experience);
                if(sizeof($completedOrders)!=0){
                    throw new BadRequestHttpException("You can't change status because this event has completed order");
                }
            }

            $experience->setStatus($newStatus);
            $this->entityManager->persist($experience);
            $this->entityManager->flush();
        }


        return true;
    }

}
