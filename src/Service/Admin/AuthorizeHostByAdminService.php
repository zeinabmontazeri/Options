<?php

namespace App\Service\Admin;

use App\Entity\Host;
use App\Repository\HostRepository;
use App\Request\AuthorizeAdminRequest;

class AuthorizeHostByAdminService
{
    public function authorizeHost(
        Host                  $host,
        HostRepository        $hostRepository,
        AuthorizeAdminRequest $request): void
    {
        $hostRepository->updateHostApprovalStatus($host->getId(), $request->approvalStatus);
    }
}