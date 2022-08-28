<?php

namespace App\Service\Shop;

use App\Entity\Order;
use App\Repository\OrderRepository;

class RemoveOrderService
{
    public function removeOrder(
        Order           $order,
        OrderRepository $orderRepository): bool
    {
        $orderRepository->remove($order, true);
        return true;
    }
}