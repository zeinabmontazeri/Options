<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Host;
use App\Entity\User;


class HostExperienceReportControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setFixtureFromSourceName(['AppFixtures']);
    }
    public function testHostCanGetTotalReport()
    {
        $token = $this->getToken(User::ROLE_HOST);
        $this->client->request('GET', "/api/v1/hosts/report", []
            , [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ]);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'GetHostExperienceTotalReport');
    }

    public function testHostCanGetPreciseReport()
    {
        $token = $this->getToken(User::ROLE_HOST);
        $user = $this->getUser();
        $experienceId = $user->getHost()
            ->getExperiences()
            ->first()
            ->getId();
        $this->client->request('GET', "/api/v1/hosts/experiences/$experienceId/report", []
            , [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ]);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'GetHostExperiencePreciseReport');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}