<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\EnumGender;
use App\Entity\EnumOrderStatus;
use App\Entity\Event;
use App\Entity\Experience;
use App\Entity\Host;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
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
            ->setFirstName('Mehdi')
            ->setLastName('Seta')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setGender(EnumGender::MALE->value)
            ->setRoles([User::ROLE_ADMIN]);
        $manager->persist($user);
        $manager->flush();

        $user1 = new User();
        $user1->setPassword($this->hasher->hashPassword($user, 'pass_12345'))
            ->setPhoneNumber('09919979109')
            ->setFirstName('Kakashi')
            ->setLastName('Hatake')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setGender(EnumGender::MALE->value)
            ->setRoles([User::ROLE_HOST]);
        $manager->persist($user1);
        $manager->flush();

        $user2 = new User();
        $user2->setPassword($this->hasher->hashPassword($user, 'pass_123456'))
            ->setPhoneNumber('09136971826')
            ->setFirstName('Naruto')
            ->setLastName('Uzumaki')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setGender(EnumGender::MALE->value)
            ->setRoles([User::ROLE_EXPERIENCER]);
        $manager->persist($user2);
        $manager->flush();

        $user3 = new User();
        $user3->setPassword($this->hasher->hashPassword($user, 'pass_123456'))
            ->setPhoneNumber('09101503620')
            ->setFirstName('Susuke')
            ->setLastName('Uchiha')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setGender(EnumGender::MALE->value)
            ->setRoles([User::ROLE_EXPERIENCER]);
        $manager->persist($user3);
        $manager->flush();

        $host = new Host();
        $host->setUser($user2)
            ->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($host);
        $manager->flush();

        $host = new Host();
        $host->setUser($user3)
            ->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($host);
        $manager->flush();

        $category = new Category();
        $category->setName('cat-1');
        $manager->persist($category);
        $manager->flush();


        $experience = new Experience();
        $experience->setHost($host)
            ->setCategory($category)
            ->setTitle('this title by host 1')
            ->setDescription('this description by host 1')
            ->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($experience);
        $manager->flush();


        $event = new Event();
        $event->setCreatedAt(new \DateTimeImmutable())
            ->setAddress('Isfahan')
            ->setCapacity(20)
            ->setDuration(90)
            ->setExperience($experience)
            ->setIsOnline(false)
            ->setPrice('2000')
            ->setStartsAt(new \DateTime());
        $manager->persist($event);
        $manager->flush();

        $event2 = new Event();
        $event2->setCreatedAt(new \DateTimeImmutable())
            ->setAddress('Isfahan')
            ->setCapacity(20)
            ->setDuration(90)
            ->setExperience($experience)
            ->setIsOnline(false)
            ->setPrice('2000')
            ->setStartsAt(new \DateTime("1990-10-10"));
        $manager->persist($event2);
        $manager->flush();

        $order = new Order();
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setEvent($event);
        $order->setPayablePrice('2000');
        $order->setStatus(EnumOrderStatus::DRAFT->value);
        $order->setUser($user2);
        $manager->persist($order);
        $manager->flush();

        $order1 = new Order();
        $order1->setCreatedAt(new \DateTimeImmutable());
        $order1->setEvent($event);
        $order1->setPayablePrice('2000');
        $order1->setStatus(EnumOrderStatus::DRAFT->value);
        $order1->setUser($user3);
        $manager->persist($order1);
        $manager->flush();

    }
}
