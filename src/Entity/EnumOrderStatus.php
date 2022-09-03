<?php

namespace App\Entity;
enum EnumOrderStatus: string
{
    case CHECKOUT = 'CHECKOUT';
    case DRAFT = 'DRAFT';
}