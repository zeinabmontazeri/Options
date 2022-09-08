<?php

namespace App\Entity\Enums;
enum EnumOrderStatus: string
{
    case CHECKOUT = 'CHECKOUT';
    case DRAFT = 'DRAFT';
}