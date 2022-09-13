<?php

namespace App\Entity\Enums;

enum TransactionStatusEnum: string
{
    case Pending = 'STATUS_PENDING';
    case Success = 'STATUS_SUCCESS';
    case Failure = 'STATUS_FAILURE';
}
