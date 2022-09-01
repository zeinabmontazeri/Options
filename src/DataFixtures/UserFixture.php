<?php

namespace App\DataFixtures;

use App\Entity\EnumGender;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
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
                ->setRoles($faker->randomElements(['ROLE_USER', 'ROLE_HOST', 'ROLE_ADMIN'], $faker->numberBetween(1, 3)));
            $manager->persist($user);
            $manager->flush();
        }

    }
}
