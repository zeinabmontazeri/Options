<?php

namespace App\Exception;

use Exception;

class AuthException extends Exception
{
    public function __construct(public $message, public $statusCode)
    {
    }
}