<?php

namespace App\Factories\Transformer;

use App\Entity\Enums\EnumHostBusinessClassStatus;

class EnumHostBusinessClassStatusDataType
{
    public function ConvertToObject(mixed $value)
    {
        switch ($value) {
            case 'GOLD':
                return EnumHostBusinessClassStatus::GOLD;
            case 'BRONZE':
                return EnumHostBusinessClassStatus::BRONZE;
            case 'SILVER':
                return EnumHostBusinessClassStatus::SILVER;
            case 'NORMAL':
                return EnumHostBusinessClassStatus::NORMAL;
        }
    }
}