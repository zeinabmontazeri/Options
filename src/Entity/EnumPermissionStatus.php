<?php

namespace App\Entity;

enum EnumPermissionStatus: string
{
    case ACCEPTED = 'ACCEPTED';
    case PENDING = 'PENDING';
    case REJECTED = 'REJECTED';
}
