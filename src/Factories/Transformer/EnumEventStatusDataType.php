<?php

namespace App\Factories\Transformer;

use App\Entity\Enums\EnumEventStatus;

class EnumEventStatusDataType
{
    public function ConvertToObject(mixed $value)
    {
        switch ($value) {
            case 'PUBLISHED':
                return EnumEventStatus::PUBLISHED;
            case 'DRAFT':
                return EnumEventStatus::DRAFT;
            case 'CANCELED':
                return EnumEventStatus::CANCELED;
        }
    }
}