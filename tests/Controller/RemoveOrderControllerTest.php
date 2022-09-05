<?php

namespace App\Tests\Controller;

use App\Entity\Order;
use App\Entity\User;
use JetBrains\PhpStorm\NoReturn;

class RemoveOrderControllerTest extends BaseTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->setFixtureFromSourceName(['UserFixtures', 'RemoveOrderFixtures']);
    }

    #[NoReturn] public function testRemoveOrder()
    {

        $token = $this->getToken(User::ROLE_EXPERIENCER);
        $orders = $this->entityManager->getRepository(Order::class)->findAll();
        $orderId = $orders[0]->getId();
        $this->client->request('DELETE', "/api/v1/shop/orders/{$orderId}/remove/", []
            , [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ]);


        $response = $this->client->getResponse();
        $orders = $this->entityManager->getRepository(Order::class)->findAll();
        $decodedResponse = json_decode($response->getContent());
        $this->assertResponseCode($response, 200);
        $this->assertCount(0, $orders);
        $this->assertEquals('success', $decodedResponse->status);

    }
}