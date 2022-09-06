<?php

namespace App\Factories\Transformer;

use App\Entity\Enums\EnumOrderStatus;

class EnumOrderStatusDataType
{
    public function ConvertToObject(mixed $value)
    {
        switch ($value) {
            case 'CHECKOUT':
                return EnumOrderStatus::CHECKOUT;
            case 'DRAFT':
                return EnumOrderStatus::DRAFT;
        }
    }
}