<?php

namespace App\Entity\Enums;

enum EnumPermissionStatus: string
{
    case ACCEPTED = 'ACCEPTED';
    case PENDING = 'PENDING';
    case REJECTED = 'REJECTED';
}
