<?php

namespace App\Service\Host;

use App\Entity\Enums\EnumHostBusinessClassStatus;
use App\Repository\HostRepository;
use App\Repository\OrderRepository;
use DateTime;
use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class HostBusinessClassService
{
    use UpdateHostBusinessClassTrait;


    /**
     * @throws Exception
     */
    public function setBusinessClass(
        HostRepository  $hostRepository,
        OrderRepository $orderRepository,
        string          $fromDate,
        string          $toDate): void
    {
        $fromDateFormatted = new DateTime($fromDate);
        $toDateFormatted = new DateTime($toDate);
        $interval = $fromDateFormatted->diff($toDateFormatted);
        if ($interval->days > 62) {
            throw new BadRequestHttpException('Date range cannot be more than 62 days');
        }

        $hostSalesData = $orderRepository->getHostSalesForSetBusinessClass($fromDate, $toDate);
        if (empty($hostSalesData)) {
            throw new BadRequestHttpException('No data found for the given date range');
        }
        $chunkedHostSalesData = array_chunk($hostSalesData, (ceil(count($hostSalesData)/3)));

        $chunkCount = count($chunkedHostSalesData);
        if ($chunkCount == 1) {
            $this->updateHostBusinessClass(
                $hostRepository,
                $chunkedHostSalesData[0],
                EnumHostBusinessClassStatus::GOLD);
        }
        if ($chunkCount == 2) {
            $this->updateHostBusinessClass(
                $hostRepository,
                $chunkedHostSalesData[0],
                EnumHostBusinessClassStatus::GOLD);
            $this->updateHostBusinessClass(
                $hostRepository,
                $chunkedHostSalesData[1],
                EnumHostBusinessClassStatus::SILVER);
        }
        if ($chunkCount == 3) {
            $this->updateHostBusinessClass(
                $hostRepository,
                $chunkedHostSalesData[0],
                EnumHostBusinessClassStatus::GOLD);
            $this->updateHostBusinessClass(
                $hostRepository,
                $chunkedHostSalesData[1],
                EnumHostBusinessClassStatus::SILVER);
            $this->updateHostBusinessClass(
                $hostRepository,
                $chunkedHostSalesData[2],
                EnumHostBusinessClassStatus::BRONZE);

        }

    }

}