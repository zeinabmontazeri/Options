<?php

namespace App\Service\Host;

use App\Repository\OrderRepository;

class HostBusinessClassService
{
    public function setBusinessClass(OrderRepository $orderRepository, string $fromDate, string $toDate): void
    {
        $orderRepository->getHostSalesForSetBusinessClas($fromDate, $toDate);

    }

}