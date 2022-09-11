<?php

namespace App\Factories\Transformer;

use App\Entity\Enums\EnumEventStatus;
use App\Entity\Enums\EnumGender;
use App\Entity\Enums\EnumHostBusinessClassStatus;
use App\Entity\Enums\EnumOrderStatus;
use App\Entity\Enums\EnumPermissionStatus;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DataTypeFactory
{
    public function getObject(string $type)
    {
        if ($type == 'DateTimeInterface')
            return new DateTimeDataType();
        if ($type == 'int')
            return new IntegerDataType();
        if ($type == 'string')
            return new StringDataType();
        if ($type == 'bool')
            return new BooleanDataType();
        if ($type == EnumPermissionStatus::class)
            return new EnumPermissionStatusDataType();
        if ($type == EnumEventStatus::class)
            return new EnumEventStatusDataType();
        if ($type == EnumHostBusinessClassStatus::class){
            return new EnumHostBusinessClassStatusDataType();
        }
        if ($type == EnumOrderStatus::class){
            return new EnumOrderStatusDataType();
        }
        if ($type == EnumGender::class)
            return new EnumGenderDataType();
        if ($type == UploadedFile::class)
            return new FileDataType();

        throw new NotFoundHttpException();
    }
}