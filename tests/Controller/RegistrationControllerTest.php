<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;


class RegistrationControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setFixtureFromSourceName(['UserFixtures']);
    }


    /**
     * @throws \Exception
     */
    public function testUserCanRegister()
    {
        $registerCredential = (object)[
            "phoneNumber"=> "09191467719",
            "birthDate"=> "1993-06-03 12:09:50",
            "password"=> "12345dsf",
            "gender"=> "MALE",
            "role"=> "ROLE_HOST",
            "firstName"=> "مهرداد",
            "lastName"=> "محمدی"
        ];

        $this->client->request('POST', '/api/v1/auth/register', []
            , [], [
                'CONTENT_TYPE' => 'application/json',
            ],json_encode($registerCredential));

        $response = $this->client->getResponse();
        $this->assertResponse($response , 'UserRegister/successfulRegister');
    }

    /**
     * @throws \Exception
     */
    public function testUserCantRegisterWithMissingFields()
    {
        $registerCredential = (object)[];

        $this->client->request('POST', '/api/v1/auth/register', []
            , [], [
                'CONTENT_TYPE' => 'application/json',
            ],json_encode($registerCredential));

        $response = $this->client->getResponse();
        $this->assertResponse($response , 'UserRegister/missingFields',400);
    }

    /**
     * @throws \Exception
     */
    public function testUserCantRegisterWithWrongPhoneFormat()
    {
        $registerCredential = (object)[
            "phoneNumber"=> "99191467719",
            "birthDate"=> "1993-06-03 12:09:50",
            "password"=> "12345dsf",
            "gender"=> "MALE",
            "role"=> "ROLE_HOST",
            "firstName"=> "مهرداد",
            "lastName"=> "محمدی"
        ];

        $this->client->request('POST', '/api/v1/auth/register', []
            , [], [
                'CONTENT_TYPE' => 'application/json',
            ],json_encode($registerCredential));

        $response = $this->client->getResponse();
        $this->assertResponse($response , 'UserRegister/wrongFormat',400);
    }

    /**
     * @throws \Exception
     */
    public function testUserCantRegisterWithDuplicatePhoneNumber()
    {
        $registerCredential = (object)[
            "phoneNumber"=> "09919979109",
            "birthDate"=> "1993-06-03 12:09:50",
            "password"=> "12345dsf",
            "gender"=> "MALE",
            "role"=> "ROLE_HOST",
            "firstName"=> "مهرداد",
            "lastName"=> "محمدی"
        ];

        $this->client->request('POST', '/api/v1/auth/register', []
            , [], [
                'CONTENT_TYPE' => 'application/json',
            ],json_encode($registerCredential));

        $response = $this->client->getResponse();
        $this->assertResponse($response , 'UserRegister/DuplicatePhonenumber',400);
    }
//    /**
//     * @throws \Exception
//     */
//    public function testOthersCouldNotCanGetAllCategories()
//    {
//        $token = $this->getToken(User::ROLE_HOST);
//        $this->client->request('GET', '/api/v1/admins/categories', []
//            , [], [
//                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
//                'CONTENT_TYPE' => 'application/json',
//            ]);
//
//        $response = $this->client->getResponse();
//        $this->assertResponse($response , 'accessDenied', 401);
//    }

}
