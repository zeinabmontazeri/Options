<?php

namespace App\Controller\Shop;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\Shop\OrderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/api/shop')]
class OrderController extends AbstractController
{
    #[Route('/experiencerOrder/{id}', name: 'app_shop_experiencer_order' , methods: 'GET')]
    public function getExperiencerOrder(OrderService $orderService ,User $user): Response
    {
        $res = $orderService->getUserOrders($user->getId());
        return $this->json([
            'data' => $res,
            'status' => true,
            'message' => 'get all user\'s orders successfully'
        ], Response::HTTP_OK);
    }
}
