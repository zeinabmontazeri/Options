<?php

namespace App\Service;

use App\Auth\AuthenticatedUser;
use App\Entity\EnumOrderStatus;
use App\Entity\Experience;
use App\Entity\Host;
use App\Entity\User;
use App\Repository\ExperienceRepository;
use App\Repository\OrderRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HostReportService
{
    private $security;

    public function __construct(AuthenticatedUser $security)
    {
        $this->security = $security;
    }

    /**
     * @throws JWTDecodeFailureException
     */
    public function totalReport(
        OrderRepository      $orderRepository,
        Host                 $host,
        ExperienceRepository $experienceRepository): array
    {
        if ($this->security->getRole() != User::ROLE_ADMIN)
            if ($this->security->getUser() !== $host->getUser())
                throw new AccessDeniedHttpException("You are not allowed to perform this action.");
        $orders = $orderRepository->findAll();
        $totalIncome = 0;
        $totalOrderCount = 0;
        $totalEventCount = 0;
        foreach ($orders as $order) {
            if ($order->getStatus() == EnumOrderStatus::CHECKOUT->value) {
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

    public function preciseReport(
        Host            $host,
        Experience      $experience,
        OrderRepository $orderRepository): array
    {
        if ($this->security->getUser() !== $host->getUser())
            throw new NotFoundHttpException("Resource not found.");
        $orders = $orderRepository->findAll();
        $totalIncomePerExperience = 0;
        $totalOrderCountPerExperience = 0;
        $totalEventCountPerExperience = count($experience->getEvents());
        foreach ($orders as $order) {
            if ($order->getStatus() == EnumOrderStatus::CHECKOUT->value) {
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