<?php

namespace App\Tests\DataFixtures\ORM;


use App\Entity\Enums\EnumGender;
use App\Entity\Enums\EnumHostBusinessClassStatus;
use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Host;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setPassword($this->hasher->hashPassword($user, 'pass_1234'))
            ->setPhoneNumber('09225075485')
            ->setFirstName('Itachi')
            ->setLastName('Uchiha')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setGender(EnumGender::MALE->value)
            ->setRoles([User::ROLE_ADMIN]);
        $manager->persist($user);
        $manager->flush();

        $user1 = new User();
        $user1->setPassword($this->hasher->hashPassword($user, 'pass_1234'))
            ->setPhoneNumber('09919979109')
            ->setFirstName('Kakashi')
            ->setLastName('Hatake')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setGender(EnumGender::MALE->value)
            ->setRoles([User::ROLE_HOST]);
        $manager->persist($user1);
        $manager->flush();

        $user2 = new User();
        $user2->setPassword($this->hasher->hashPassword($user, 'pass_1234'))
            ->setPhoneNumber('09136971826')
            ->setFirstName('Naruto')
            ->setLastName('Uzumaki')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setGender(EnumGender::MALE->value)
            ->setRoles([User::ROLE_EXPERIENCER]);
        $manager->persist($user2);
        $manager->flush();

        $host = new Host();
        $host->setUser($user2)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setApprovalStatus(EnumPermissionStatus::ACCEPTED)
            ->setLevel(EnumHostBusinessClassStatus::NORMAL);
        $manager->persist($host);
        $manager->flush();
    }
}
