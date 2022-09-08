<?php

namespace App\Factories\Transformer;

use App\Entity\Enums\EnumGender;

class EnumGenderDataType
{
    public function ConvertToObject(mixed $value)
    {
        switch ($value) {
            case 'FEMALE':
                return EnumGender::FEMALE;
            case 'MALE':
                return EnumGender::MALE;
        }
    }
}