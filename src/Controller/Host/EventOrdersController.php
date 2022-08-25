<?php

namespace App\Controller\Host;

use App\Entity\EnumGender;
use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;
use App\Repository\OrderRepository;
use App\Service\EventService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class EventOrdersController extends AbstractController
{
    #[Route('/host/event/order/{id}', name: 'app_host_event_orders', methods: ['GET'])]
    public function index(Event $event, EventService $service, OrderRepository $orderRepository): Response
    {
        $orders = $service->getOrders($orderRepository, $event);
        $totalIncome = $orderRepository->getTotalIncomeAnEvent($event);
        return $this->json([
            "Event Data" => [
                "Experience Title" => $event->getExperience()->getTitle(),
                "Event Capacity" => $event->getCapacity(),
                "Event Remaining Capacity" => $event->getCapacity() - count($orders)
            ],
            "Total Income" => $totalIncome,
            "List of orders" => $orders
        ]);
    }
}
