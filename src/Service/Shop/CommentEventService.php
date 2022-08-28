<?php

namespace App\Service\Shop;
use App\Entity\Comment;
use App\Entity\EnumOrderStatus;
use App\Entity\Event;
use App\Entity\User;
use App\Exception\InvalidInputException;
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
        return $this->result;
    }
    private function checkUserExistence($userId)
    {
        $this->user = $this->userRepository->find($userId);
        if ($this->user == null) {
            throw new InvalidInputException('The userId not exists',400);
        }
    }
    private function checkEventExistenceAndPassed($eventId)
    {
        $this->event = $this->eventRepository->find($eventId);
        if ($this->event == null) {
            throw new InvalidInputException('The eventId not exists',400);
        }
        $fishedDateTime=$this->event->getStartsAt()->add(new DateInterval('PT'.$this->event->getDuration().'M'));
        if (new \DateTimeImmutable()<$fishedDateTime) {
            throw new InvalidInputException('The event time is not over yet',400);
        }
    }
    private function checkUserOrderedEvent($userId,$eventId)
    {
        $resultArray=$this->orderRepository->findByUserEvent_id_Status($userId,$eventId);
        if(empty($resultArray))
        {
            throw new InvalidInputException('The user has not previously ordered an event',400);
        }
        if($resultArray[0]['status']!=EnumOrderStatus::CHECKOUT->value)
        {
            throw new InvalidInputException('The event has not yet been paid',400);
        }
    }
    private function commentOnEventValidation($userId,$eventId): bool
    {
       $this->checkUserExistence($userId);
       $this->checkEventExistenceAndPassed($eventId);
       $this->checkUserOrderedEvent($userId, $eventId);
        return true;
    }
}