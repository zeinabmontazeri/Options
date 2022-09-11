<?php

namespace App\Payment\CmdHandler;

use App\Payment\Bank\LinkInterface;

interface CmdHandlerInterface
{
    public function validate(): bool;
    public function doPersist(): bool;
    public function run(LinkInterface $link);
}
