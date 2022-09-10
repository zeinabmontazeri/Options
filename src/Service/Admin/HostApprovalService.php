<?php

namespace App\Service\Admin;

use App\Entity\Host;
use App\Repository\HostRepository;
use App\Repository\UpgradeRequestRepository;
use App\Request\AuthorizeAdminRequest;

class HostApprovalService
{
    public function __construct(private UpgradeRequestRepository $upgradeRequestRepository)
    {
    }

    public function getPendingList(): array
    {
        return $this->upgradeRequestRepository->findAll();
    }

    public function authorizeHost(
        Host                  $host,
        HostRepository        $hostRepository,
        AuthorizeAdminRequest $request): void
    {
        $hostRepository->updateHostApprovalStatus($host->getId(), $request->approvalStatus);
    }
}