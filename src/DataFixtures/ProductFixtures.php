<?php

namespace App\DataFixtures;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for($i = 0 ; $i < 10 ; $i++){
            $product = new Product();
            $product->setTitle($faker->jobTitle());
            $product->setDescription($faker->realText());
            $product->setImgUrl($faker->imageUrl());
            $product->setStock($faker->randomNumber(2));
            $product->setPrice($faker->randomFloat(2,0,100));

            $randomIndex = $faker->numberBetween(0,1);
            $category = $this->getReference(CategoryFixtures::CATEGORY_REFERENCE. $randomIndex, Category::class);
            $product->addCategory($category);
            $manager->persist($product);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
