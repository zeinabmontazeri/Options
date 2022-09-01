<?php

namespace App\DataFixtures;


use App\Entity\Event;
use App\Entity\Experience;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DeActiveEventFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $experiences = $manager->getRepository(Experience::class)->findAll();
        $experiencesWithActiveEventsCount = array_slice($experiences, sizeof($experiences) / 2);
        for ($i = 0; $i < 10; $i++) {
            $event = new Event();
            $event->setRegisteredUsers($faker->numberBetween(10))
                ->setExperience($faker->randomElement($experiencesWithActiveEventsCount))
                ->setCapacity(10)
                ->setDuration(120)
                ->setPrice($faker->numberBetween(100, 1000))
                ->setStartsAt($faker->dateTimeBetween('-3 year', '-1 year'))
                ->setCreatedAt($faker->dateTime);
            $manager->persist($event);
            $isOnline = $faker->numberBetween(0, 1);
            if ($isOnline) {
                $event->setIsOnline(1)
                    ->setLink($faker->url);
            } else {
                $event->setIsOnline(0)
                    ->setAddress($faker->address);
            }
            $manager->persist($event);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ExperienceFixture::class];
    }
}