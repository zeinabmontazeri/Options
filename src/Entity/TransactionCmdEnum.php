<?php

namespace App\Entity;

enum TransactionCmdEnum: string
{
    case Payment = 'CMD_PAYMENT';
    case Verify = 'CMD_VERIFY';
    case Settle = 'CMD_SETTLE';
    case Reversal = 'CMD_REVERSAL';
    case PaymentResponse = 'CMD_PAYMENT_RESPONSE';
}
