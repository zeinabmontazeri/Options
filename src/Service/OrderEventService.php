<?php

namespace App\Service;

use App\Entity\EnumOrderStatus;
use App\Entity\Event;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;

class OrderEventService
{
    private ?User $user = null;
    private ?Event $event = null;
    public function __construct(private readonly UserRepository $userRepository,
                                private readonly EventRepository $eventRepository,
                                private readonly OrderRepository $orderRepository)
{
}
    public function orderTheEvent($userId,$eventId):int
{
    if($this->orderValidation($userId,$eventId))
    {
        $order = new Order();
        $order->setUser($this->user);
        $order->setEvent($this->event);
        $order->setPayablePrice($this->event->getPrice());
        $order->setStatus(EnumOrderStatus::DRAFT->value);

        $this->orderRepository->add($order, true);
        return $order->getId();
    }
    return 0;
}
    private function checkUserExistence($userId):void
{
    $this->user = $this->userRepository->find($userId);
    if ($this->user == null) {
        throw new \Exception('The userId not exists',400);
    }
}
    private function checkEventExistence($eventId): void
{
    $this->event = $this->eventRepository->find($eventId);
    if ($this->event == null) {
        throw new \Exception('The eventId not exists',400);
    }
}
    private function orderValidation($userId,$eventId): bool
{
    $this->checkUserExistence($userId);
    $this->checkEventExistence($eventId);
    $this->checkUserOrderedEvent($userId,$eventId);
    if($this->event->getCapacity()>$this->orderRepository->getTotalRegisteredEvent($eventId))
    {
        if($this->event->getStartsAt()>new \DateTimeImmutable())
        {
            return true;
        }
        else
        {
            $message='The event registration time is over';
        }
    }
    else
    {
        $message='The event registration capacity is full';
    }
    throw new \Exception($message,400);
}
    private function checkUserOrderedEvent($userId,$eventId): void
{
    $orderId=$this->orderRepository->findByUserEvent_Id($userId,$eventId);
    if($orderId!=0)
    {
        throw new \Exception('The user ordered event before',400);
    }
}
}