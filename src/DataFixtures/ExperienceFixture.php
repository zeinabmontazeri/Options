<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Experience;
use App\Entity\Host;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ExperienceFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $categories = $manager->getRepository(Category::class)->findAll();
        $hosts = $manager->getRepository(Host::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            $experience = new Experience();
            $experience->setTitle($faker->word)
                ->setDescription($faker->word)
                ->setHost($faker->randomElement($hosts))
                ->setCategory($faker->randomElement($categories))
                ->setCreatedAt($faker->dateTime);
            $manager->persist($experience);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixture::class,
            HostFixture::class,
        ];
    }
}
