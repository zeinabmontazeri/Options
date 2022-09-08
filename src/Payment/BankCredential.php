<?php

namespace App\Payment;

class BankCredential
{
    public function __construct(
        private readonly string $userName,
        private readonly string $password,
        private readonly string $terminalID
    ) {
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getTerminalId()
    {
        return $this->terminalID;
    }
}
