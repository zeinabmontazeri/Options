<?php

namespace App\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(
    event: 'lexik_jwt_authentication.on_authentication_success',
    method: 'setResponseFormat',
    priority: -10
)]
final class AuthenticationSuccessListener
{
    public function setResponseFormat(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();
        $response = [
            'success'=> true,
            'data'=> [
                'id' => $user->getId(),
                'token'=> $data['token'],
                'refresh_token'=>$data['refresh_token']
            ],
            'message' => 'User authenticated successfuly.',
        ];
        $event->setData($response);
    }
}
