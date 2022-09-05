<?php

namespace App\Entity;

enum TransactionOriginEnum: string
{
    case Order = 'ORIGIN_ORDER';
}
