<?php

namespace App\Controller\Shop;

use App\Auth\AcceptableRoles;
use App\Auth\AuthenticatedUser;
use App\Entity\Event;
use App\Entity\User;
use App\Request\CommentOnEventRequest;
use App\Service\Shop\CommentEventService;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1')]
class CommentController extends AbstractController
{
    /**
     * @throws JWTDecodeFailureException
     */
    #[Route('/events/{event_id}/comments', name: 'app_shop_comment_event', requirements: ['event_id' => '\d+'], methods: 'POST')]
    #[ParamConverter('event', class: Event::class, options: ['id' => 'event_id'])]
    #[AcceptableRoles(User::ROLE_ADMIN, User::ROLE_HOST, User::ROLE_EXPERIENCER, User::ROLE_GUEST)]
    public function commentOnEvent(
        Event                 $event,
        CommentOnEventRequest $validatedRequest,
        CommentEventService   $commentEventService,
        AuthenticatedUser     $security): Response
    {
        $comment = $validatedRequest->comment;
        $result = $commentEventService->commentTheEvent($security->getUser(), $event, $comment);
        return $this->json([
            'data' => $result['data'],
            'message' => $result['message'],
            'status' => $result['status'],
        ], Response::HTTP_OK);
    }
}