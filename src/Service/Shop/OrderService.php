<?php

namespace App\Service\Shop;

use App\Repository\OrderRepository;

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