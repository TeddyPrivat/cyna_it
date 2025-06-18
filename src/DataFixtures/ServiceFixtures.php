<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ServiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for($i = 0 ; $i < 10 ; $i++){
            $service = new Service();
            $service->setTitle($faker->jobTitle());
            $service->setDescription($faker->text);
            $service->setPrice($faker->randomFloat(2,0, 100));
            $service->setStock($faker->randomNumber(2));
            $manager->persist($service);
        }
        $manager->flush();
    }
}
