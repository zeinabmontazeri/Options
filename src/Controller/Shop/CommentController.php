<?php

namespace App\Controller\Shop;

use App\Request\CommentOnEventRequest;
use App\Service\Shop\CommentEventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('api/v1/shop')]
class CommentController extends AbstractController
{
    #[Route('/commentOnEvent', name: 'app_shop_comment_event',methods: ['POST'])]
    #[ParamConverter('validatedRequest', class: CommentOnEventRequest::class)]
    public function commentOnEvent(CommentOnEventRequest $validatedRequest,CommentEventService $commentEventService):Response
    {
        $userId=$validatedRequest->userId;
        $eventId=$validatedRequest->eventId;
        $description=$validatedRequest->description;
        $result=$commentEventService->commentTheEvent($userId,$eventId,$description);
        return $this->json([
            'data' => $result['data'],
            'message' =>$result['message'],
            'status' => $result['status']
        ], $result['status']?200:400);
    }
}
