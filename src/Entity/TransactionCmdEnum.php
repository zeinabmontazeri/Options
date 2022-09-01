<?php

namespace App\Entity;

enum TransactionCmdEnum: string
{
    case Payment = 'CMD_PAYMENT';
    case Verify = 'CMD_VERIFY';
    case Inquery = 'CMD_INQUERY';
    case Reversal = 'CMD_REVERSAL';
}
