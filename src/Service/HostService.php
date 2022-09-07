<?php

namespace App\Service;

use App\Entity\Host;
use App\Repository\CommissionRepository;

class HostService
{
    public function __construct(private CommissionRepository $commissionRepository)
    {
    }

    public function getTotalCommissions(Host $host): int
    {
        $totals = $this->commissionRepository->getByHost($host->getId());
        return  intval(array_pop($totals)['total']);
    }

}