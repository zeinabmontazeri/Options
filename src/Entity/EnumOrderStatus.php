<?php

namespace App\Entity;
enum EnumOrderStatus: string
{
    case CHECKOUT = 'checkout';
    case PENDING = 'pending';
    case DRAFT = 'draft';

}