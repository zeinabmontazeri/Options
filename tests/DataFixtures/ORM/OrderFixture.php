<?php

namespace App\Tests\DataFixtures\ORM;

use App\Entity\Enums\EnumOrderStatus;
use App\Entity\Event;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OrderFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $events = $manager->getRepository(Event::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();
        for ($i = 0; $i < 30; $i++) {
            $order = new Order();
            $event = $faker->randomElement($events);
            $order->setUser($faker->randomElement($users))
                ->setEvent($event)
                ->setStatus($faker->randomElement(EnumOrderStatus::cases()))
                ->setPayablePrice($event->getPrice())
                ->setCreatedAt($faker->dateTime);
            $manager->persist($order);
        }
        $manager->flush();
    }
}
