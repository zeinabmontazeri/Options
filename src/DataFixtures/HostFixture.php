<?php

namespace App\DataFixtures;

use App\Entity\Enums\EnumHostBusinessClassStatus;
use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Host;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class HostFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();
        $users = $manager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $host = new Host();
            if (in_array('ROLE_HOST', $user->getRoles())) {
                $host->setUser($user)
                    ->setApprovalStatus($faker->randomElement(EnumPermissionStatus::cases()))
                    ->setLevel(EnumHostBusinessClassStatus::NORMAL)
                    ->setCreatedAt($faker->dateTime);
                $manager->persist($host);
            }
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixture::class];
    }

}
