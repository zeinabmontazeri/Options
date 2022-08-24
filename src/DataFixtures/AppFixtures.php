<?php

namespace App\DataFixtures;

use App\Entity\EnumGender;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('asdasdas')
            ->setPhoneNumber('09131008141')
            ->setPassword('23133')
            ->setFirstName('sadasdas')
            ->setLastName('sadasdas')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setGender(EnumGender::MALE);
        $manager->persist($user);
        $manager->flush();
    }
}
