<?php

namespace App\Factories\Transformer;

use App\Entity\Enums\EnumPermissionStatus;

class EnumPermissionStatusDataType
{
    public function ConvertToObject(mixed $value)
    {
        switch ($value) {
            case 'ACCEPTED':
                return EnumPermissionStatus::ACCEPTED;
            case 'PENDING':
                return EnumPermissionStatus::PENDING;
            case 'REJECTED':
                return EnumPermissionStatus::REJECTED;
        }
    }
}