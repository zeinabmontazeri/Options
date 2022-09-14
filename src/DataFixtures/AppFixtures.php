<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Enums\EnumEventStatus;
use App\Entity\Enums\EnumGender;
use App\Entity\Enums\EnumHostBusinessClassStatus;
use App\Entity\Enums\EnumOrderStatus;
use App\Entity\Enums\EnumPermissionStatus;
use App\Entity\Event;
use App\Entity\Experience;
use App\Entity\Host;
use App\Entity\Media;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(protected UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $experiencer1 = new User();
        $experiencer1->setFirstName('Arash')
            ->setLastName('Moradi')
            ->setGender(EnumGender::MALE)
            ->setBirthDate(new \DateTimeImmutable('2002'))
            ->setRoles(["ROLE_EXPERIENCER"])
            ->setPassword($this->hasher->hashPassword($experiencer1, '1234'))
            ->setPhoneNumber('09120000000');
        $manager->persist($experiencer1);
        $manager->flush();

        $experiencer2 = new User();
        $experiencer2->setFirstName('Seyed Sadegh')
            ->setLastName('Marashi')
            ->setGender(EnumGender::MALE)
            ->setBirthDate(new \DateTimeImmutable('1993'))
            ->setRoles(["ROLE_HOST"])
            ->setPassword($this->hasher->hashPassword($experiencer2, '1234'))
            ->setPhoneNumber('09121111111');
        $manager->persist($experiencer2);
        $manager->flush();

        $host2 = new Host();
        $host2->setUser($experiencer2)
            ->setApprovalStatus(EnumPermissionStatus::PENDING)
            ->setLevel(EnumHostBusinessClassStatus::NORMAL);
        $manager->persist($host2);
        $manager->flush();

        $admin = new User();
        $admin->setFirstName('Seyed Mahdi')
            ->setLastName('Setayande')
            ->setGender(EnumGender::MALE)
            ->setBirthDate(new \DateTimeImmutable('1999'))
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->hasher->hashPassword($admin, '1234'))
            ->setPhoneNumber('09122222222');
        $manager->persist($admin);
        $manager->flush();

        $experiencer3 = new User();
        $experiencer3->setFirstName('Elmira')
            ->setLastName('Behnava')
            ->setGender(EnumGender::FEMALE)
            ->setBirthDate(new \DateTimeImmutable('1999'))
            ->setRoles(["ROLE_HOST"])
            ->setPassword($this->hasher->hashPassword($experiencer3, '1234'))
            ->setPhoneNumber('09123333333');
        $manager->persist($experiencer3);
        $manager->flush();

        $host1 = new Host();
        $host1->setLevel(EnumHostBusinessClassStatus::NORMAL)
            ->setApprovalStatus(EnumPermissionStatus::ACCEPTED)
            ->setUser($experiencer3);
        $manager->persist($host1);
        $manager->flush();


        $faker = Factory::create();
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setPassword($this->hasher->hashPassword($user, 'pass_1234'))
                ->setPhoneNumber($faker->regexify('/^09\d{9}$/'))
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setCreatedAt($faker->dateTime)->setGender($faker->randomElement(EnumGender::cases()))
                ->setRoles($faker->randomElements(['ROLE_HOST']));
            $manager->persist($user);
            $host = new Host();
            $host->setLevel(EnumHostBusinessClassStatus::NORMAL)
                ->setApprovalStatus(EnumPermissionStatus::ACCEPTED)
                ->setUser($user);
            $manager->persist($host);
            $manager->flush();
        }

        $category1 = new Category();
        $category1->setName('Art');
        $manager->persist($category1);
        $manager->flush();

        $category2 = new Category();
        $category2->setName('Sport');
        $manager->persist($category2);
        $manager->flush();

        $category3 = new Category();
        $category3->setName('Fun');
        $manager->persist($category3);
        $manager->flush();

        $category4 = new Category();
        $category4->setName('uncategorized');
        $manager->persist($category4);
        $manager->flush();

        $experience = new Experience();
        $experience->setApprovalStatus(EnumPermissionStatus::PENDING)
            ->setCategory($category1)
            ->setStatus(EnumEventStatus::DRAFT)
            ->setDescription("Read poem together and at the end we will see a movie")
            ->setTitle("Reading Poem")
            ->setHost($host1);
        $manager->persist($experience);
        $manager->flush();

        $media = new Media();
        $media->setExperience($experience)
            ->setFileName('12esdFFSAD.png');
        $manager->persist($media);
        $manager->flush();

        $experience2 = new Experience();
        $experience2->setApprovalStatus(EnumPermissionStatus::ACCEPTED)
            ->setCategory($category2)
            ->setStatus(EnumEventStatus::PUBLISHED)
            ->setDescription("Play football in real stadium.")
            ->setTitle("PLaying Football")
            ->setHost($host1);
        $manager->persist($experience2);
        $manager->flush();

        $media = new Media();
        $media->setExperience($experience2)
            ->setFileName('asd11ffgAD.jpg');
        $manager->persist($media);
        $manager->flush();


        //capacity full event
        $event = new Event();
        $event->setStatus(EnumEventStatus::PUBLISHED)
            ->setStartsAt(new \DateTime('2023-10-10'))
            ->setPrice('30000')
            ->setIsOnline(false)
            ->setExperience($experience2)
            ->setDuration(100)
            ->setCapacity(22)
            ->setAddress('Azadi Stadium')
            ->setRegisteredUsers(22);
        $manager->persist($event);
        $manager->flush();

        //purchasable event
        $event2 = new Event();
        $event2->setStatus(EnumEventStatus::DRAFT)
            ->setStartsAt(new \DateTime('2023-6-10'))
            ->setPrice('20000')
            ->setIsOnline(false)
            ->setExperience($experience2)
            ->setDuration(100)
            ->setCapacity(22)
            ->setAddress('Naghshe Jahan Stadium')
            ->setRegisteredUsers(10);
        $manager->persist($event2);
        $manager->flush();

        //purchasable order
        $order1 = new Order();
        $order1->setStatus(EnumOrderStatus::DRAFT)
            ->setUser($experiencer1)
            ->setPayablePrice(20000)
            ->setEvent($event2);
        $manager->persist($order1);
        $manager->flush();

        //for capacity full and could not purchase order
        $order2 = new Order();
        $order2->setEvent($event)
            ->setPayablePrice(30000)
            ->setUser($experiencer1)
            ->setStatus(EnumOrderStatus::DRAFT);
        $manager->persist($order2);
        $manager->flush();

        $comment = new Comment();
        $comment->setUser($experiencer1)
            ->setEvent($event2)
            ->setComment('any meals serve?');
        $manager->persist($comment);
        $manager->flush();



    }

}
