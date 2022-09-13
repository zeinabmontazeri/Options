<?php

namespace App\Listener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[AsEventListener(
    event:  'Symfony\Component\Security\Http\Event\LogoutEvent',
    method: 'onLogout',
    priority: -100
)]
final class JwtLogoutListener
{
    public function onLogout(LogoutEvent $event): void
    {
         $response = $event->getResponse();
         $status_code = $response->getStatusCode();
         $content = json_decode($response->getContent());

        $response->setContent(json_encode([
            'status' => $status_code==200?'success':'fail',
            'data' => null,
            'message' =>$content->message??'',
        ]));
        $event->setResponse($response);
    }
}
