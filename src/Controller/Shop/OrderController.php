<?php

namespace App\Controller\Shop;

use App\Auth\AcceptableRoles;
use App\Entity\User;
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
    #[AcceptableRoles(User::ROLE_EXPERIENCER)]
    #[Route('/orders/{id}/remove', name: 'app_remove_order', requirements: ['id' => '\d+'], methods: ["DELETE"])]
    public function removeOrder(
        Request            $request,
        RemoveOrderService $removeOrderService,
        OrderRepository    $orderRepository): JsonResponse
    {

        $order = $orderRepository->find((int)($request->get('id')));
        if ($order and $order->getStatus() == 'draft') {
            $result = $removeOrderService->removeOrder($order, $orderRepository);
            return $this->json([
                'message' => 'Order Removed Successfully.',
                'status' => 'success'],
                Response::HTTP_OK);
        } else {
            return $this->json([
                'message' => 'Order Id is not correct.',
                'status' => 'failed'],
                Response::HTTP_BAD_REQUEST);
        }
    }
}
