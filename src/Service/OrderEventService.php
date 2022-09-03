<?php

namespace App\Service;

use App\Entity\Enums\EnumEventStatus;
use App\Entity\Enums\EnumOrderStatus;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
   if($event->getStatus()==EnumEventStatus::PUBLISHED) {
       $this->checkUserOrderedEvent($user->getId(), $event->getId());
       if ($event->getCapacity() > $this->orderRepository->getTotalRegisteredEvent($event->getId())) {
           if ($event->getStartsAt() > new \DateTimeImmutable()) {
               return true;
           } else {
               $message = 'The event registration time is over';
           }
       } else {
           $message = 'The event registration capacity is full';
       }
   }else {
       $message = 'This event has not yet been published';
   }
    throw new BadRequestHttpException($message);
}
    private function checkUserOrderedEvent($userId,$eventId): void
{
    $orderId=$this->orderRepository->findByUserEvent_Id($userId,$eventId);
    if($orderId!=0)
    {
        throw new BadRequestHttpException('The user ordered event before');
    }
}
}