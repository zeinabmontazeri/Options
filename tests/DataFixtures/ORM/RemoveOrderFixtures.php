<?php

namespace App\Tests\DataFixtures\ORM;

use App\Entity\Category;
use App\Entity\Enums\EnumEventStatus;
use App\Entity\Enums\EnumOrderStatus;
use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Event;
use App\Entity\Experience;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class RemoveOrderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $userExperiencer = $manager->getRepository(User::class)->findOneBy(['phoneNumber' => '09136971826']);
        $hostUser = $manager->getRepository(User::class)->findOneBy(['phoneNumber' => '09919979109'])->getHost();

        $faker = Factory::create();

        $category = new Category();
        $category->setName($faker->word)
            ->setCreatedAt($faker->dateTime);
        $manager->persist($category);
        $manager->flush();

        $experience = new Experience();
        $experience->setTitle($faker->word)
            ->setDescription($faker->word)
            ->setHost($hostUser)
            ->setCategory($category)
            ->setStatus(EnumEventStatus::DRAFT)
            ->setApprovalStatus(EnumPermissionStatus::ACCEPTED)
            ->setCreatedAt($faker->dateTime);
        $manager->persist($experience);
        $manager->flush();

        $event = new Event();
        $event->setRegisteredUsers($faker->numberBetween(1, 9))
            ->setExperience($experience)
            ->setCapacity($faker->numberBetween(10, 100))
            ->setDuration(120)
            ->setPrice($faker->numberBetween(100, 1000))
            ->setStartsAt($faker->dateTimeBetween('+1 year', '+4 year'))
            ->setStatus(EnumEventStatus::PUBLISHED)
            ->setCreatedAt($faker->dateTime)
            ->setIsOnline(1);
        $manager->persist($event);
        $manager->flush();

        $order = new Order();
        $order->setUser($userExperiencer)
            ->setEvent($event)
            ->setStatus(EnumOrderStatus::DRAFT)
            ->setPayablePrice($event->getPrice())
            ->setCreatedAt($faker->dateTime);
        $manager->persist($order);
        $manager->flush();

    }
}