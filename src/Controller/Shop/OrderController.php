<?php

namespace App\Controller\Shop;

use App\Repository\OrderRepository;
use App\Service\Shop\RemoveOrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/shop')]
class OrderController extends AbstractController
{
    #[Route('/orders/{id}/remove', name: 'app_remove_order', methods: ["DELETE"])]
    public function index(
        Request            $request,
        RemoveOrderService $removeOrderService,
        OrderRepository    $orderRepository): JsonResponse
    {
        $order = $orderRepository->find(json_decode($request->get('id')));

        if ($order and $order->getStatus() == 0) {
            $result = $removeOrderService->removeOrder($order, $orderRepository);
            return $this->json([
                'message' => $result['message'],
                'status' => $result['status']],
                Response::HTTP_OK);
        } else {
            return $this->json([
                'message' => 'Order Id is not correct.',
                'status' => 'failed'],
                Response::HTTP_BAD_REQUEST);
        }
    }
}
