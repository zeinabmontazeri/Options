<?php

namespace App\Service\Admin;

use App\Auth\AuthenticatedUser;
use App\Entity\Enums\EnumHostBusinessClassStatus;
use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Host;
use App\Entity\UpgradeRequest;
use App\Repository\HostRepository;
use App\Repository\UpgradeRequestRepository;
use App\Request\AuthorizeAdminRequest;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HostApprovalService
{
    public function __construct(
        private UpgradeRequestRepository $upgradeRequestRepository,
        private EntityManagerInterface $entityManager,
        private AuthenticatedUser $security
    )
    {
    }

    public function changeUpgradeStatus(UpgradeRequest $request,EnumPermissionStatus $status)
    {
        $this->entityManager->getConnection()->beginTransaction(); // suspend auto-commit
        try {
            $request->setStatus($status);

            $this->entityManager->persist($request);
            $this->entityManager->flush();

            if ($status == EnumPermissionStatus::ACCEPTED) {
                $user = $request->getUser();
                $user->setRoles(['ROLE_HOST']);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $host = new Host();
                $host->setApprovalStatus(EnumPermissionStatus::ACCEPTED);
                $host->setUser($user);
                $host->setLevel(EnumHostBusinessClassStatus::NORMAL);

                $this->entityManager->persist($host);
                $this->entityManager->flush();
            }
            $this->entityManager->getConnection()->commit();

        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }

    public function getPendingList($page=1,$perPage = 20): array
    {
        return $this->upgradeRequestRepository->getPendingRequests($page,$perPage);
    }

    public function addUpgradeRequest(){
        $user = $this->security->getUser();
        $existingRequest = $this->upgradeRequestRepository->findOneBy(['user'=>$user,'status'=>EnumPermissionStatus::PENDING]);

        if($existingRequest != null)
            throw new BadRequestHttpException('You already have a pending request for upgrade');

        $req = new UpgradeRequest();
        $req->setUser($user);
        $req->setMessage("Wants to be host as existing user");

        $this->entityManager->persist($req);
        $this->entityManager->flush();
    }
}