<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Host;
use App\Entity\User;


class HostExperienceControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setFixtureFromSourceName(['AppFixtures']);
    }

    public function testHostCanGetAllOwnExperience()
    {
        $token = $this->getToken(User::ROLE_HOST);
        $user = $this->getUser();
        $host = $this->entityManager
            ->getRepository(Host::class)
            ->findOneBy(['user' => $user, 'approvalStatus' => EnumPermissionStatus::ACCEPTED]);
        $hostId = $host->getId();
        $this->client->request('GET', "/api/v1/hosts/$hostId/experiences", []
            , [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ]);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'GetExperiences');
    }

    public function testHostCanCreateExperience()
    {
        $token = $this->getToken(User::ROLE_HOST);
        $user = $this->getUser();
        $host = $this->entityManager
            ->getRepository(Host::class)
            ->findOneBy(['user' => $user, 'approvalStatus' => EnumPermissionStatus::ACCEPTED]);
        $hostId = $host->getId();
        $content = [
            'title' => 'this title.',
            'description' => ' this is description.',
            'category_name' => 'cat-1'
        ];
        $this->client->request('Post', "/api/v1/hosts/$hostId/experiences", []
            , [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ], json_encode($content));
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'CreateExperience');
    }

}