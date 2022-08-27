<?php

namespace App\Service\Shop;
use App\Entity\Comment;
use App\Entity\EnumOrderStatus;
use App\Entity\Event;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\EventRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use DateInterval;
use Exception;

class CommentEventService
{
    private ?User $user = null;
    private ?Event $event = null;
    private $result=[];
    public function __construct(private readonly UserRepository $userRepository,
                                private readonly EventRepository $eventRepository,
                                private readonly OrderRepository $orderRepository,
                                private readonly CommentRepository $commentRepository)
    {
    }
    public function commentTheEvent($userId, $eventId, $description):array
    {
        if($this->commentOnEventValidation($userId,$eventId))
        {
            $comment = new Comment();
            $comment->SetUser($this->user);
            $comment->setEvent($this->event);
            $comment->setDescription($description);
            $this->commentRepository->add($comment, true);
            $this->result['status'] =true;
            $this->result['data']=['commentId'=>$comment->getId()];
            $this->result['message']='The user commented successfully';
        }
        else
        {
            $this->result['status'] =false;
            $this->result['data']=[];
        }
        return $this->result;
    }
    private function checkUserExistence($userId):bool
    {
        $this->user = $this->userRepository->find($userId);
        if ($this->user == null) {
            $this->result['message']='The userId not exists';
            return false;
        }
        return true;
    }
    private function checkEventExistenceAndPassed($eventId): bool
    {
        $this->event = $this->eventRepository->find($eventId);
        if ($this->event == null) {
            $this->result['message']='The eventId not exists';
            return false;
        }
        $fishedDateTime=$this->event->getStartsAt()->add(new DateInterval('PT'.$this->event->getDuration().'M'));
        if (new \DateTimeImmutable()<$fishedDateTime) {
            $this->result['data']=[];
            $this->result['message']='The event time is not over yet';
            return false;
        }
        return true;
    }
    private function checkUserOrderedEvent($userId,$eventId): bool
    {
        $resultArray=$this->orderRepository->findByUserEvent_id_Status($userId,$eventId);
        if(empty($resultArray))
        {
            $this->result['message']='The user has not previously ordered an event';
            return false;
        }
        if($resultArray[0]['status']!=EnumOrderStatus::CHECKOUT->value)
        {
            $this->result['message']='The event has not yet been paid';
            return false;
        }
        return true;
    }
    private function commentOnEventValidation($userId,$eventId): bool
    {
        if($this->checkUserExistence($userId)) {
            if($this->checkEventExistenceAndPassed($eventId)) {
               return $this->checkUserOrderedEvent($userId, $eventId);
            }
        }
        return false;
    }
}