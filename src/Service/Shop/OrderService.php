<?php

namespace App\Service\Shop;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;

class OrderService
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }
    public function getUserOrders($userId)
    {
        return $this->orderRepository->getExperiencerOrder($userId);
    }
}