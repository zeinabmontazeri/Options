<?php

namespace App\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpKernel\KernelInterface;

class DataBasePrimer
{
    public static function prime(KernelInterface $kernel)
    {
        if ($kernel->getEnvironment() !== 'test')
            throw new \LogicException("Primer should execute on test environment.");
        $entityManager = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadatas);
    }
}
