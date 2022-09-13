<?php

namespace App\Tests\Controller;

use App\Entity\Experience;

class ShopExperienceListExperienceControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setFixtureFromSourceName(['GetExperineceByFilterFixture']);
    }

    public function testGetAllExperiences()
    {
        $experiences = $this->entityManager->getRepository(Experience::class)->findAll();
        $this->client->request('GET', '/api/v1/experiences', []
            , [], [
                'CONTENT_TYPE' => 'application/json',
            ]);
        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent());
        $this->assertResponseCode($response, 200);
        $this->assertEquals('success', $decodedResponse->status);
        $this->assertSameSize($decodedResponse->data, $experiences);
    }

    public function testGetExperienceByFilter()
    {
        $experiences = $this->entityManager->getRepository(Experience::class)->findAll();
        $this->client->request('GET', '/api/v1/experiences',
            ["host"=>1, "purchasable"=>true, "category"=>1]
            , [], [
                'CONTENT_TYPE' => 'application/json',
            ]);
        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent());
        $this->assertResponseCode($response, 200);
        $this->assertEquals('success', $decodedResponse->status);
        $this->assertEquals(count($decodedResponse->data), count($experiences)/2);
        $this->assertEquals($experiences[0]->getTitle(), $decodedResponse->data[0]->title);
        $this->assertEquals($decodedResponse->data[0]->title, $experiences[0]->getTitle());

    }
}