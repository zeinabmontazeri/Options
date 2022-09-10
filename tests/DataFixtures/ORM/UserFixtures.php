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
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const EXPERIENCER_USER_REFERENCE = 'experiencer-user';
    public const HOST_USER_REFERENCE = 'host-user';

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $adminUser = new User();
        $adminUser->setPassword($this->hasher->hashPassword($adminUser, 'pass_1234'))
            ->setPhoneNumber('09225075485')
            ->setFirstName('Itachi')
            ->setLastName('Uchiha')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setGender(EnumGender::MALE)
            ->setRoles([User::ROLE_ADMIN]);
        $this->addReference(self::ADMIN_USER_REFERENCE, $adminUser);
        $manager->persist($adminUser);
        $manager->flush();

        $user1 = new User();
        $user1->setPassword($this->hasher->hashPassword($user1, 'pass_1234'))
            ->setPhoneNumber('09919979109')
            ->setFirstName('Kakashi')
            ->setLastName('Hatake')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setGender(EnumGender::MALE)
            ->setRoles([User::ROLE_HOST]);
        $manager->persist($user1);
        $manager->flush();

        $experiencerUser = new User();
        $experiencerUser->setPassword($this->hasher->hashPassword($experiencerUser, 'pass_1234'))
            ->setPhoneNumber('09136971826')
            ->setFirstName('Naruto')
            ->setLastName('Uzumaki')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setGender(EnumGender::MALE)
            ->setRoles([User::ROLE_EXPERIENCER]);
        $manager->persist($experiencerUser);
        $this->addReference(self::EXPERIENCER_USER_REFERENCE, $experiencerUser);

        $manager->flush();

        $hostUser = new Host();
        $hostUser->setUser($user1)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setApprovalStatus(EnumPermissionStatus::ACCEPTED)
            ->setLevel(EnumHostBusinessClassStatus::NORMAL);
        $manager->persist($hostUser);
        $this->addReference(self::HOST_USER_REFERENCE, $hostUser);

        $manager->flush();
    }
}
