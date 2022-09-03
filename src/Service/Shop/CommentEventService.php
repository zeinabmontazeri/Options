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
    private $result=[];
    public function __construct(private readonly CommentRepository $commentRepository)
    {
    }
    public function commentTheEvent($user, $event, $comment):array
    {
            $commentobj = new Comment();
            $commentobj->SetUser($user);
            $commentobj->setEvent($event);
            $commentobj->setComment($comment);
            $this->commentRepository->add($commentobj, true);
            $this->result['status'] ='success';
            $this->result['data']=['commentId'=>$commentobj->getId()];
            $this->result['message']='The user commented successfully';
            return $this->result;
    }
}