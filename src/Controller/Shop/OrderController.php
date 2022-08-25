<?php

namespace App\Controller\Shop;

use App\Request\OrderEventRequest;
use App\Service\OrderEventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/api/shop')]
class OrderController extends AbstractController
{
    /*
      * todo:
      * @Security("has_role('ROLE_experiencer')")
      * */
    #[Route('/orderEvent', name: 'app_shop_order_event',methods: ['POST'])]
    #[ParamConverter('validatedRequest', class: OrderEventRequest::class)]
    public function OrderAnEvent(OrderEventRequest $validatedRequest,OrderEventService $orderEventService): JsonResponse
    {
        $userId=$validatedRequest->userId;
        $eventId=$validatedRequest->eventId;
        $orderId=$orderEventService->orderTheEvent($userId,$eventId);
        $orderEventResponse = [
            'data' =>['orderId'=>$orderId],
            'message'=> 'success',
            'status' => true,
        ];
        return new JsonResponse($orderEventResponse, 200);
    }
}
