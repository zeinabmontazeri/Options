<?php

namespace App\DTO;

use App\Entity\Enums\EnumPermissionStatus;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Collection implements CollectionInterface
{
    protected array $result = [];

    public function toArray(array $entities, array $groups = null): array
    {
        $result = [];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $dateNormalizer = new DateTimeNormalizer();
        $enumNormalizer = new BackedEnumNormalizer();
        $serializer = new Serializer([$enumNormalizer , $dateNormalizer , $normalizer]);

        foreach ($entities as $entity)
        {
            $data = $serializer->normalize($entity, null, ['groups' => $groups]);
            $result[] = $data;
        }
        return $result;
    }
}