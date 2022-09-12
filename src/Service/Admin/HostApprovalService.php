<?php

namespace App\Service\Admin;

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

class HostApprovalService
{
    public function __construct(
        private UpgradeRequestRepository $upgradeRequestRepository,
        private EntityManagerInterface $entityManager
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

    public function authorizeHost(
        Host                  $host,
        HostRepository        $hostRepository,
        AuthorizeAdminRequest $request): void
    {
        $hostRepository->updateHostApprovalStatus($host->getId(), $request->approvalStatus);
    }
}