<?php

namespace App\Service\Shop;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;

class OrderService
{
    private ?User $user = null;
    public function __construct(private readonly UserRepository $userRepository,
                                private readonly OrderRepository $orderRepository)
    {
    }
    public function getUserOrders($userId)
    {
        $this->checkUserExistence($userId);
       return $this->orderRepository->getExperiencerOrder($userId);
    }
    private function checkUserExistence($userId):void
    {
        $this->user = $this->userRepository->find($userId);
        if ($this->user == null) {
            throw new \Exception('The userId not exists',400);
        }
    }

}