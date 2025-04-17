<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
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
            $manager->persist($product);
        }
        $manager->flush();
    }
}
