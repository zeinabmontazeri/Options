<?php

namespace App\Payment\Bank;

interface LinkResponseInterface
{
    public function getStatus(): bool;
    public function getBankToken(): ?string;
    public function getBankStatus(): int;
    public function getBankStatusMessage(): ?string;
    public function getRedirectLink(): ?string;
}
