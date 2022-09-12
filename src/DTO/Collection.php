<?php

namespace App\DTO;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CategoryCollection implements CollectionInterface
{
    protected array $result = [];

    public function toArray(array $entities , array $groups = null): array
    {
        $result = [];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        foreach ($entities as $entity) {
            $data = $serializer->normalize($entity, null, ['groups' => $groups]);
            $result[] = $data;
        }
        return $result;
    }
}