<?php

namespace App\Controller\Shop;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/shop")]
class RemoveOrderController extends AbstractController
{
    #[Route('/remove/order/{id}', name: 'app_remove_order', methods: ["DELETE"])]
    public function index(Order $order, OrderRepository $orderRepository): JsonResponse
    {
        $orderRepository->remove($order, true);
        return $this->json(['message' => 'Order removed Successfully', 'status' => 'success'], Response::HTTP_OK);
    }
}
