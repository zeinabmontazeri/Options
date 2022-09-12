<?php

namespace App\Controller\Shop;

use App\Auth\AcceptableRoles;
use App\Auth\AuthenticatedUser;
use App\Entity\Enums\EnumOrderStatus;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\OrderRepository;
use App\Request\OrderEventRequest;
use App\Service\OrderCheckoutService;
use App\Service\OrderEventService;
use App\Service\Shop\OrderService;
use App\Service\Shop\RemoveOrderService;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1')]
class OrderController extends AbstractController
{
    /**
     * @throws JWTDecodeFailureException
     */
    #[Route('/orders/{order_id}', name: 'app_remove_order', requirements: ['id' => '\d+'], methods: ["DELETE"])]
    #[ParamConverter('event', class: Order::class, options: ['id' => 'order_id'])]
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
    #[Route('/order/events', name: 'app_shop_order_event', requirements: ['event_id' => '\d+'], methods: ['POST'])]
    #[AcceptableRoles(User::ROLE_EXPERIENCER)]
    public function OrderAnEvent(
        OrderEventRequest $request,
        EventRepository   $eventRepository,
        OrderEventService $orderEventService,
        AuthenticatedUser $security): Response
    {
        $result = $orderEventService->orderTheEvent($security->getUser(), $request, $eventRepository);
        return $this->json([
            'data' => $result['data'],
            'message' => $result['message'],
            'status' => $result['status'],

        ], Response::HTTP_OK);
    }

    /**
     * @throws JWTDecodeFailureException
     */
    #[Route('/orders', name: 'app_shop_users_order', methods: 'GET')]
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

    #[Route(
        '/orders/{order_id<\d+>}/checkout',
        name: 'app_order_checkout',
        methods: 'GET',
    )]
    #[AcceptableRoles(User::ROLE_EXPERIENCER)]
    public function orderCheckout(int $order_id, OrderCheckoutService $orderCheckoutService): RedirectResponse
    {
        $redirectLink = $orderCheckoutService->checkout($order_id);
        return $this->redirect($redirectLink, Response::HTTP_SEE_OTHER);
    }
}
