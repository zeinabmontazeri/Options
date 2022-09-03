<?php

namespace App\Entity;

enum EnumEventStatus: string
{
    case PUBLISHED = 'PUBLISHED';
    case DRAFT = 'DRAFT';
    case CANCELED = 'CANCELED';
}