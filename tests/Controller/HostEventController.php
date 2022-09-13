<?php

namespace App\Tests\Controller;

use App\Entity\Event;

class HostEventController extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setFixtureFromSourceName(['UserFixture', 'HostFixture', 'CategoryFixture', 'ExperienceFixture', 'ActiveEventFixture', 'OrderFixture']);
    }

    public function testHostCanGetEventReport()
    {
        $events = $this->entityManager->getRepository(Event::class)->findAll();
        foreach ($events as $event) {
            $phoneNumber = $event->getExperience()->getHost()->getUser()->getPhoneNumber();
            $token = $this->getTokenWithLogin($phoneNumber, 'ROLE_HOST');
            $event_id = $event->getId();
            $this->client->request(
                'GET', "/api/v1/host/events/$event_id/report", []
                , [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json'
            ]);
            $response = $this->client->getResponse();
            $this->assertSame(200, $response->getStatusCode());
        }
    }
}