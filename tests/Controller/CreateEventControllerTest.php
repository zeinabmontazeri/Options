<?php

namespace App\Tests\Controller;

use App\Entity\Experience;
use App\Entity\User;
use Faker\Factory;

class CreateEventControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setFixtureFromSourceName(['UserFixture', 'HostFixture', 'CategoryFixture', 'ExperienceFixture']);
    }

    public function testCreateEvent()
    {
        for ($i = 1; $i <= 20; $i++) {
            $experience = $this->entityManager->getRepository(Experience::class)->find($i);
            $phoneNumber = $experience->getHost()->getUser()->getPhoneNumber();
            $token = $this->getTokenWithLogin($phoneNumber, 'ROLE_HOST');
            $experience_id = $experience->getId();
            $faker = Factory::create();
            $isOnline = $faker->numberBetween(0, 1);
            $link = null;
            $address = null;
            if ($isOnline) {
                $link = $faker->url;
            } else {
                $address = $faker->address;
            }
            $content = [
                "capacity" => $faker->numberBetween(10, 100),
                "duration" => $faker->numberBetween(60, 120),
                "price" =>(string) $faker->numberBetween(1000, 10000),
                "isOnline" => $isOnline,
                "startsAt" => $faker->dateTimeBetween('+1 year', '+4 year')->format('Y-m-d H:i:s'),
                "link" => $link,
                "address" => $address
            ];
            $this->client->request(
                'POST', "/api/v1/hosts/experiences/$experience_id/events/create", []
                , [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ], json_encode($content)
            );
            $response = $this->client->getResponse();
            $this->assertSame(200, $response->getStatusCode());
        }
    }
}