<?php

namespace App\Entity;

enum TransactionStatusEnum: string
{
    case Pending = 'STATUS_PENDING';
    case Success = 'STATUS_SUCCESS';
    case Failure = 'STATUS_FAILURE';
}
