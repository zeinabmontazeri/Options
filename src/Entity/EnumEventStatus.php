<?php

namespace App\Entity;

enum EnumEventStatus: string
{
    case PUBLISHED = 'published';
    case DRAFT = 'draft';
}
