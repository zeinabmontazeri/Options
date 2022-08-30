<?php

namespace App\Entity;
enum EnumPaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILURE = 'failure';

}