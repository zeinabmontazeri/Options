<?php

namespace App\Service\Shop;
use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Entity\EnumEventStatus;
use App\Exception\InvalidInputException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CommentEventService
{
    private $result=[];
    public function __construct(private readonly CommentRepository $commentRepository)
    {
    }
    public function commentTheEvent($user, $event, $comment):array
    {
            if($event->getStatus() !== EnumEventStatus::PUBLISHED)
                throw new BadRequestHttpException('Event is not published');
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