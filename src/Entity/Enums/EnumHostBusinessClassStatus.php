<?php

namespace App\Entity\Enums;

enum EnumHostBusinessClassStatus: string
{
    case GOLD = 'GOLD';
    case BRONZE = 'BRONZE';
    case SILVER = 'SILVER';
    case NORMAL = 'NORMAL';
}
