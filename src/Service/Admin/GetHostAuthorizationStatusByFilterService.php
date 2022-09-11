<?php

namespace App\Service\Admin;

use App\DTO\DtoFactory;
use App\Repository\HostRepository;
use App\Request\HostAuthorizationFilterRequest;

class GetHostAuthorizationStatusByFilterService
{

    public function getHostByAuthorizationStatus(
        HostRepository                 $hostRepository,
        HostAuthorizationFilterRequest $hostAuthorizationFilterRequest

    ): array
    {
        $hosts = $hostRepository->getHostByAuthorizationStatus($hostAuthorizationFilterRequest);
        $hostCollection = DtoFactory::getInstance('host');
        return $hostCollection->toArray($hosts);
    }
}