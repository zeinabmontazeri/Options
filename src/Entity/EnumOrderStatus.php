<?php

namespace App\Entity;
enum EnumOrderStatus: string
{
    case SUCCESS = 'success';
    case FAIL = 'fail';
    case DRAFT = 'draft';
    case CHECKOUT='checkout';

}