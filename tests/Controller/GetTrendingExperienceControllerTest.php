<?php

namespace App\Tests\Controller;

use App\Entity\Experience;

class GetTrendingExperienceControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setFixtureFromSourceName(['GetTrendingExperienceFixture']);
    }

    public function testGetTrendingExperience()
    {
        $experiences = $this->entityManager->getRepository(Experience::class)->getTrendingExperiences();

        $this->client->request('GET', '/api/v1/shop/experiences/trending/', []
            , [], [
                'CONTENT_TYPE' => 'application/json',
            ]);
        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent());
        $this->assertResponseCode($response, 200);
        $this->assertSameSize($experiences, $decodedResponse->data);
    }
}