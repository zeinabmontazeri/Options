<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function index(): Response
    {
        return new Response(
            'Hello Options!'
        );
    }
}
