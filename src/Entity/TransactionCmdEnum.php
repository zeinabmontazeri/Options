<?php

namespace App\Entity;

enum TransactionCmdEnum: string
{
    case Payment = 'CMD_PAYMENT';
    case Verify = 'CMD_VERIFY';
    case Settle = 'CMD_SETTLE';
    case Inquery = 'CMD_INQUERY';
    case Reversal = 'CMD_REVERSAL';
    case Refund = 'CMD_REFUND';
    case PayResponse = 'CMD_PAYMENT_RESPONSE';
}
