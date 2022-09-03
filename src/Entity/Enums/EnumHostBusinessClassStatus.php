<?php

namespace App\Entity\Enums;

enum EnumHostBusinessClassStatus:string
{
    case GOLDEN = 'GOLDEN';
    case BRONZE = 'BRONZE';
    case SILVER = 'SILVER';
    case NORMAL = 'NORMAL';
}
