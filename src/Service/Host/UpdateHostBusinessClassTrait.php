<?php

namespace App\Service\Host;

use App\Entity\Enums\EnumHostBusinessClassStatus;
use App\Repository\HostRepository;

trait UpdateHostBusinessClassTrait
{
    public function updateHostBusinessClass(
        HostRepository              $hostRepository,
        array                       $hostSaleDataChunk,
        EnumHostBusinessClassStatus $businessClass): void
    {
        foreach ($hostSaleDataChunk as $host) {
            $hostId = $host['hostId'];
            $hostRepository->updateHostBusinessClass($hostId, $businessClass->value);
        }
    }

}