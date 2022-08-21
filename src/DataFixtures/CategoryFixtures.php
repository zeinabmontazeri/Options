<?php
namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName('category  '.$i);
            $category->setCreatedAt(new \DateTimeImmutable('2020-2-'.mt_rand(1, 30)));
            $manager->persist($category);
        }

        $manager->flush();
    }
}

