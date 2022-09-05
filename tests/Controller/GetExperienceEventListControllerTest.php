<?php

namespace App\Tests\Controller;

use App\Entity\Experience;

class GetExperienceEventListControllerTest extends BaseTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->setFixtureFromSourceName(['GetExperienceEventListFixture']);
    }

    public function testGetExperienceEventList(){

        $experiences = $this->entityManager->getRepository(Experience::class)->find(1);
        $experienceEvents = $experiences->getEvents();
        $experienceId = $experiences->getId();

        $this->client->request('GET', "/api/v1/shop/experiences/{$experienceId}/events/", []
            , [], [
                'CONTENT_TYPE' => 'application/json',
            ]);
        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent());
        $this->assertResponseCode($response, 200);
        $this->assertEquals('success', $decodedResponse->status);
        $this->assertSameSize($experienceEvents, $decodedResponse->data);
        $this->assertEquals($experienceEvents[0]->getId(), $decodedResponse->data[0]->id);
    }
}