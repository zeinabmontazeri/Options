<?php

namespace App\Service;

use App\Entity\EnumOrderStatus;
use App\Entity\Experience;
use App\Entity\Host;
use App\Repository\ExperienceRepository;
use App\Repository\OrderRepository;

class HostReportService
{

    public function totalReport(OrderRepository $orderRepository, Host $host, ExperienceRepository $experienceRepository): array
    {
        $orders = $orderRepository->findAll();
        $totalIncome = 0;
        $totalOrderCount = 0;
        $totalEventCount = 0;
        foreach ($orders as $order) {
            if($order->getStatus() == EnumOrderStatus::CHECKOUT->value) {
                $event = $order->getEvent();
                $experience = $event->getExperience();
                if ($experience->getHost() === $host) {
                    $totalIncome += $order->getPayablePrice();
                    $totalOrderCount++;
                }
            }
        }
        $experiences = $experienceRepository->findBy(['host' => $host]);
        foreach ($experiences as $experience) {
            $totalEventCount += count($experience->getEvents());
        }
        $res = ['message' => 'Successfully retrieve total report.', 'status' => true];
        $res['data']['totalIncome'] = $totalIncome;
        $res['data']['totalEvent'] = $totalEventCount;
        $res['data']['totalOrder'] = $totalOrderCount;
        return $res;
    }

    public function preciseReport(Host $host, Experience $experience, OrderRepository $orderRepository): array
    {
        $orders = $orderRepository->findAll();
        $totalIncomePerExperience = 0;
        $totalOrderCountPerExperience = 0;
        $totalEventCountPerExperience = count($experience->getEvents());
        foreach ($orders as $order) {
            if($order->getStatus() == EnumOrderStatus::CHECKOUT->value) {
                $event = $order->getEvent();
                if ($event->getExperience()->getHost() === $host) {
                    if ($event->getExperience() === $experience) {
                        $totalIncomePerExperience += $order->getPayablePrice();
                        $totalOrderCountPerExperience++;
                    }
                }
            }
        }
        $res = ['message' => 'Successfully retrieve total report.', 'status' => true];
        $res['data']['experience_id'] = $experience->getId();
        $res['data']['totalIncome'] = $totalIncomePerExperience;
        $res['data']['totalEvent'] = $totalEventCountPerExperience;
        $res['data']['totalOrder'] = $totalOrderCountPerExperience;
        return $res;
    }

}