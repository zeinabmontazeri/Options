<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class AdminCategoryControllerTest extends BaseTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->setFixtureFromSourceName(['UserFixtures']);
    }


    /**
     * @throws \Exception
     */
    public function testAdminCanGetsAllCategories()
    {
        $token = $this->getToken(User::ROLE_ADMIN);
        $this->client->request('GET', '/api/v1/admins/categories', []
            , [], [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                'CONTENT_TYPE' => 'application/json',
            ]);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'getCategories');

    }


    public function testOthersCouldNotCanGetAllCategories()
    {
        $token = $this->getToken(User::ROLE_HOST);
            $this->client->request('GET', '/api/v1/admins/categories', []
                , [], [
                    'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
                    'CONTENT_TYPE' => 'application/json',
                ]);
            $response = $this->client->getResponse();
            $this->assertResponse($response , 'accessDenied' , 403);

    }

}
