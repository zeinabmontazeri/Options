<?php

namespace App\Service;

use App\Entity\EnumOrderStatus;
use App\Entity\Event;
use App\Entity\Order;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Repository\EventRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class OrderEventService
{
    private $result=[];
    public function __construct(private readonly OrderRepository $orderRepository)
{
}
    public function orderTheEvent($user,$event):array
{
    if($this->orderValidation($user,$event))
    {
        $order = new Order();
        $order->setUser($user);
        $order->setEvent($event);
        $order->setPayablePrice($event->getPrice());
        $order->setStatus(EnumOrderStatus::DRAFT);
        $this->orderRepository->add($order, true);
        $this->result['status'] ='success';
        $this->result['data']=['orderId'=>$order->getId()];
        $this->result['message']='The user commented successfully';
    }
    return $this->result;
}
    private function orderValidation($user,$event): bool
{
    $this->checkUserOrderedEvent($user->getId(),$event->getId());
    if($event->getCapacity()>$this->orderRepository->getTotalRegisteredEvent($event->getId()))
    {
        if($event->getStartsAt()>new \DateTimeImmutable())
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
    throw new BadRequestException($message);
}
    private function checkUserOrderedEvent($userId,$eventId): void
{
    $orderId=$this->orderRepository->findByUserEvent_Id($userId,$eventId);
    if($orderId!=0)
    {
        throw new BadRequestException('The user ordered event before');
    }
}
}