<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Enums\EnumEventStatus;
use App\Entity\Enums\EnumGender;
use App\Entity\Enums\EnumHostBusinessClassStatus;
use App\Entity\Enums\EnumOrderStatus;
use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Event;
use App\Entity\Experience;
use App\Entity\Host;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class BankTestFixture extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void

    {
        $faker = Factory::create();

        $user = new User();
        $user->setPassword($this->hasher->hashPassword($user, 'pass_1234'))
            ->setPhoneNumber("09378394659")
            ->setFirstName("elmira")
            ->setLastName("behnava")
            ->setBirthDate($faker->dateTimeBetween('-60 years', '-18 years'))
            ->setCreatedAt($faker->dateTime)
            ->setGender($faker->randomElement(EnumGender::cases()))
            ->setRoles(['ROLE_EXPERIENCER']);
        $manager->persist($user);
        $manager->flush();

        $hostUser = new User();
        $hostUser->setPassword($this->hasher->hashPassword($user, 'pass_1234'))
            ->setPhoneNumber("09121127435")
            ->setFirstName("hesan")
            ->setLastName("samimi")
            ->setBirthDate($faker->dateTimeBetween('-60 years', '-18 years'))
            ->setCreatedAt($faker->dateTime)
            ->setGender($faker->randomElement(EnumGender::cases()))
            ->setRoles(['ROLE_HOST']);
        $manager->persist($hostUser);
        $manager->flush();

        $host = new Host();
        $host->setUser($user)
            ->setApprovalStatus(EnumPermissionStatus::ACCEPTED)
            ->setLevel(EnumHostBusinessClassStatus::NORMAL)
            ->setCreatedAt($faker->dateTime);
        $manager->persist($host);
        $manager->flush();

        $category = new Category();
        $category->setName($faker->word)
            ->setCreatedAt($faker->dateTime);
        $manager->persist($category);
        $manager->flush();


        $experience = new Experience();
        $experience->setTitle($faker->word)
            ->setDescription($faker->word)
            ->setHost($host)
            ->setCategory($category)
            ->setStatus(EnumEventStatus::PUBLISHED)
            ->setApprovalStatus(EnumPermissionStatus::ACCEPTED)
            ->setCreatedAt($faker->dateTime);
        $manager->persist($experience);
        $manager->flush();

        $event = new Event();
        $event->setRegisteredUsers($faker->numberBetween(1, 9))
            ->setExperience($experience)
            ->setCapacity($faker->numberBetween(10, 100))
            ->setDuration(120)
            ->setPrice($faker->numberBetween(1000, 100000))
            ->setStartsAt($faker->dateTimeBetween('+1 year', '+4 year'))
            ->setStatus(EnumEventStatus::PUBLISHED)
            ->setCreatedAt($faker->dateTime)
            ->setIsOnline($faker->boolean);
        $manager->persist($event);
        $manager->flush();

        $order = new Order();
        $order->setUser($user)
            ->setEvent($event)
            ->setStatus(EnumOrderStatus::DRAFT)
            ->setPayablePrice($event->getPrice())
            ->setCreatedAt($faker->dateTime);
        $manager->persist($order);
        $manager->flush();

    }
}