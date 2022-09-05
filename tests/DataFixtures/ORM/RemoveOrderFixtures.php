<?php

namespace App\Tests\DataFixtures\ORM;

use App\Entity\Category;
use App\Entity\Enums\EnumEventStatus;
use App\Entity\Enums\EnumOrderStatus;
use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Event;
use App\Entity\Experience;
use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RemoveOrderFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $userExperiencer = $this->getReference(UserFixtures::EXPERIENCER_USER_REFERENCE);
        $hostUser = $this->getReference(UserFixtures::HOST_USER_REFERENCE);

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

//        $events = $manager->getRepository(Event::class)->findAll();
//        $users = $manager->getRepository(User::class)->findAll();

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