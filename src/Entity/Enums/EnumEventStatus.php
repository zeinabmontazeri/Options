<?php

namespace App\Entity\Enums;

enum EnumEventStatus: string
{
    case PUBLISHED = 'PUBLISHED';
    case DRAFT = 'DRAFT';
    case CANCELED = 'CANCELED';
}