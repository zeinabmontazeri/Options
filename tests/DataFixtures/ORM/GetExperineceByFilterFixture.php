<?php

namespace App\Tests\DataFixtures\ORM;

use App\Entity\Category;
use App\Entity\Enums\EnumEventStatus;
use App\Entity\Enums\EnumGender;
use App\Entity\Enums\EnumHostBusinessClassStatus;
use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Event;
use App\Entity\Experience;
use App\Entity\Host;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GetExperineceByFilterFixture extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void

    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setPassword($this->hasher->hashPassword($user, 'pass_1234'))
                ->setPhoneNumber($faker->regexify('/^09\d{9}$/'))
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setCreatedAt($faker->dateTime)->setGender($faker->randomElements(EnumGender::cases())[0]->value)
                ->setRoles(['ROLE_HOST']);
            $manager->persist($user);
            $manager->flush();
        }

        $users = $manager->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $host = new Host();

            $host->setUser($user)
                ->setApprovalStatus($faker->randomElement(EnumPermissionStatus::cases()))
                ->setLevel($faker->randomElement(EnumHostBusinessClassStatus::cases()))
                ->setCreatedAt($faker->dateTime);
            $manager->persist($host);

        }
        $manager->flush();

        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->word)
                ->setCreatedAt($faker->dateTime);
            $manager->persist($category);
        }
        $manager->flush();

        $categories = $manager->getRepository(Category::class)->findAll();
        $hosts = $manager->getRepository(Host::class)->findAll();

        for ($i = 0; $i < 20; $i++) {
            $experience = new Experience();
            if ($i % 2 == 0) {
                $experience->setHost($hosts[0]);
            } else {
                $experience->setHost($hosts[1]);
            }
            $experience->setTitle($faker->word)
                ->setDescription($faker->word)
                ->setCategory($faker->randomElement($categories))
                ->setStatus($faker->randomElement(EnumEventStatus::cases()))
                ->setApprovalStatus($faker->randomElement(EnumPermissionStatus::cases()))
                ->setCreatedAt($faker->dateTime);
            $manager->persist($experience);
        }
        $manager->flush();

        $experiences = $manager->getRepository(Experience::class)->findAll();
        foreach ($experiences as $experience) {
            if ($experience->getHost()->getId() % 2 == 1) {
                $event = new Event();
                $event->setRegisteredUsers($faker->numberBetween(1, 9))
                    ->setExperience($experience)
                    ->setCapacity($faker->numberBetween(20, 100))
                    ->setDuration(120)
                    ->setPrice($faker->numberBetween(100, 1000))
                    ->setStartsAt($faker->dateTimeBetween('+2 year', '+4 year'))
                    ->setStatus($faker->randomElement(EnumEventStatus::cases()))
                    ->setIsOnline(0)
                    ->setCreatedAt($faker->dateTime);
                $manager->persist($event);
            }
        }
        $manager->flush();

    }

}