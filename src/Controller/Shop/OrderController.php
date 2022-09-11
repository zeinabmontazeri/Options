<?php

namespace App\Controller\Shop;

use App\Auth\AcceptableRoles;
use App\Auth\AuthenticatedUser;
use App\Entity\Enums\EnumOrderStatus;
use App\Entity\Event;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\OrderCheckoutService;
use App\Service\OrderEventService;
use App\Service\Shop\OrderService;
use App\Service\Shop\RemoveOrderService;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/shop')]
class OrderController extends AbstractController
{
    /**
     * @throws JWTDecodeFailureException
     */
    #[Route('/orders/{id}/', name: 'app_remove_order', requirements: ['id' => '\d+'], methods: ["DELETE"])]
    #[AcceptableRoles(User::ROLE_EXPERIENCER)]
    public function index(
        Order              $order,
        RemoveOrderService $removeOrderService,
        OrderRepository    $orderRepository,
        AuthenticatedUser  $security): JsonResponse
    {
        if ($order->getStatus() == EnumOrderStatus::DRAFT and $order->getUser() === $security->getUser()) {
            $removeOrderService->removeOrder($order, $orderRepository);
            return $this->json([
                'message' => 'Order Removed Successfully.',
                'data' => [],
                'status' => 'success'],
                Response::HTTP_OK);
        } else {
            throw new AccessDeniedHttpException(
                'You are not allowed to remove this order.');
        }
    }

    /**
     * @throws JWTDecodeFailureException
     */
    #[Route('/users/events/{event_id}/order', name: 'app_shop_order_event', requirements: ['event_id' => '\d+'], methods: ['POST'])]
    #[ParamConverter('event', class: Event::class, options: ['id' => 'event_id'])]
    #[AcceptableRoles(User::ROLE_EXPERIENCER)]
    public function OrderAnEvent(Event $event, OrderEventService $orderEventService, AuthenticatedUser $security): Response
    {
        $result = $orderEventService->orderTheEvent($security->getUser(), $event);
        return $this->json([
            'data' => $result['data'],
            'message' => $result['message'],
            'status' => $result['status'],
        ], Response::HTTP_OK);
    }

    /**
     * @throws JWTDecodeFailureException
     */
    #[Route('/users/orders', name: 'app_shop_users_order', methods: 'GET')]
    #[AcceptableRoles(User::ROLE_EXPERIENCER)]
    public function getExperiencerOrder(OrderService $orderService, AuthenticatedUser $security): Response
    {
        $res = $orderService->getUserOrders($security->getUser()->getId());
        return $this->json([
            'data' => $res,
            'message' => 'get all user\'s orders successfully',
            'status' => 'success',
        ], Response::HTTP_OK);
    }

    /**
     * Checkout a draft order
     *
     * Redirect Experiencer to bank if order is purchasable
     * @OA\Tag(name="Order")
     * @OA\PathParameter (
     *      name="order_id",
     *      required=true
     * )
     * @OA\Response(
     *     response="400",
     *     description="Order is not purchasable.",
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     description="action result"
     *                 ),
     *                 @OA\Property(
     *                     property="data",
     *                     type="string",
     *                     description="A message to describe failure reason."
     *                 ),
     *                 example={
     *                         "status": "failure",
     *                         "data": "The order id(#orderId) is not purchasable."
     *                 }
     *             )
     *         )
     *     }
     * )
     * @OA\Response(
     *     response="303",
     *     description="Redirect to bank for payment.",
     * )
     */
    #[Route(
        '/orders/{order_id<\d+>}/checkout',
        name: 'app_order_checkout',
        methods: 'GET',
    )]
    #[AcceptableRoles(User::ROLE_EXPERIENCER)]
    public function orderCheckout(int $order_id, OrderCheckoutService $orderCheckoutService)
    {
        $redirectLink = $orderCheckoutService->checkout($order_id);
        return $this->redirect($redirectLink);
    }
}
