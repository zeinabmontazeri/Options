<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORY_REFERENCE = 'cat';

    public function load(ObjectManager $manager)
    {
        $cat = new Category();
        $cat->setName('category 1');
        $cat->setCreatedAt(new \DateTimeImmutable('2020-2-' . mt_rand(1, 30)));
        $manager->persist($cat);
        $manager->flush();
        $this->addReference(self::CATEGORY_REFERENCE, $cat);
        for ($i = 2; $i < 10; $i++) {
            $category = new Category();
            $category->setName('category  ' . $i);
            $category->setCreatedAt(new \DateTimeImmutable('2020-2-' . mt_rand(1, 30)));
            $manager->persist($category);
        }

        $manager->flush();

    }


}
