<?php

namespace App\Controller\Shop;
use App\Auth\AcceptableRoles;
use App\Auth\AuthenticatedUser;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\Shop\OrderService;
use App\Service\Shop\RemoveOrderService;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
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
    #[Route('/orders/{id}/remove', name: 'app_remove_order', requirements: ['id' => '\d+'], methods: ["DELETE"])]
    #[AcceptableRoles(User::ROLE_ADMIN, User::ROLE_EXPERIENCER)]
    public function index(
        Order              $order,
        RemoveOrderService $removeOrderService,
        OrderRepository    $orderRepository,
        AuthenticatedUser  $security): JsonResponse
    {
        if ($order->getStatus() == 'draft' and $order->getUser() === $security->getUser()) {
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

    #[Route('/users/orders', name: 'app_shop_users_order', methods: 'GET')]
    #[AcceptableRoles(User::ROLE_EXPERIENCER)]
    public function getExperiencerOrder(OrderService $orderService,AuthenticatedUser $security): Response
    {
        $res = $orderService->getUserOrders($security->getUser()->getId());
        return $this->json([
            'data' => $res,
            'status' => true,
            'message' => 'get all user\'s orders successfully'
        ], Response::HTTP_OK);
    }
}
