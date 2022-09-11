<?php

namespace App\Tests\DataFixtures\ORM;

use App\Entity\Category;
use App\Entity\Enums\EnumEventStatus;
use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Experience;
use App\Entity\Host;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ExperienceFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $categories = $manager->getRepository(Category::class)->findAll();
        $hosts = $manager->getRepository(Host::class)->findAll();

        for ($i = 0; $i < 20; $i++) {
            $experience = new Experience();
            $experience->setTitle($faker->word)
                ->setDescription($faker->word)
                ->setHost($faker->randomElement($hosts))
                ->setCategory($faker->randomElement($categories))
                ->setStatus($faker->randomElement(EnumEventStatus::cases()))
                ->setApprovalStatus($faker->randomElement(EnumPermissionStatus::cases()))
                ->setCreatedAt($faker->dateTime);
            $manager->persist($experience);
        }
        $manager->flush();
    }

}
