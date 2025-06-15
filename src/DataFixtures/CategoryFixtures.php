<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORY_REFERENCE = 'category_';
    public function load(ObjectManager $manager): void
    {
        $categories = ["Informatique", "Communication"];
        foreach ($categories as $i => $title) {
            $category = new Category();
            $category->setCategoryName($title);
            $manager->persist($category);
            $this->addReference(self::CATEGORY_REFERENCE . $i, $category);
         }
        $manager->flush();
    }
}
