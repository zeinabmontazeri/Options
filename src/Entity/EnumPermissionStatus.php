<?php

namespace App\Entity;

enum EnumPermissionStatus: string
{
    case ACCEPTED = 'accepted';
    case PENDING = 'pending';
    case REJECTED = 'rejected';
}