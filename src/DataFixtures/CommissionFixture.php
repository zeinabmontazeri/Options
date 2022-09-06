<?php

namespace App\DataFixtures;

use App\Entity\CommissionLevel;
use App\Entity\Enums\EnumHostBusinessClassStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CommissionFixture extends Fixture
{
    public function load(ObjectManager $manager): void

    {
        foreach (EnumHostBusinessClassStatus::cases() as $key=>$enum){
            $level = new CommissionLevel();
            $level->setName($enum->value);
            $level->setPercentage(5+$key*10);
            $manager->persist($level);
        }
        $manager->flush();
    }
}
