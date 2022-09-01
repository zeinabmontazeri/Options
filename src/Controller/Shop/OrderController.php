<?php

namespace App\Controller\Shop;

use App\Auth\AcceptableRoles;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Shop\RemoveOrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v1/shop')]
class OrderController extends AbstractController
{
    #[Route('/orders/{id}/remove', name: 'app_remove_order', requirements: ['id' => '\d+'], methods: ["DELETE"])]
    #[AcceptableRoles('ROLE_ADMIN', 'ROLE_EXPERIENCER')]
    public function index(
        Order              $order,
        RemoveOrderService $removeOrderService,
        OrderRepository    $orderRepository): JsonResponse
    {
        if ($order->getStatus() == 'draft' and $order->getUser() === $this->getUser()) {
            $removeOrderService->removeOrder($order, $orderRepository);
            return $this->json([
                'message' => 'Order Removed Successfully.',
                'data' => [],
                'status' => 'success'],
                Response::HTTP_OK);
        } else {
            return $this->json([
                'message' => 'You ar not allowed to remove this order.',
                'status' => 'failed'],
                Response::HTTP_FORBIDDEN);
        }
    }
}
