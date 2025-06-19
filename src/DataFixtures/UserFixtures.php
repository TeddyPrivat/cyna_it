<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($i = 0; $i < 5; $i++){
            $user = new User();
            $plainPassword = "123456";
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setEmail(strtolower($user->getFirstname().".".$user->getLastname().'@hotmail.fr'));
            $user->setAdress($faker->streetAddress());
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }
        $user = new User();
        $password = "123456";
        $user->setFirstname("Teddy");
        $user->setLastname("Privat");
        $user->setEmail("teddyprivat@hotmail.fr");
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setAdress($faker->streetAddress());
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $manager->persist($user);
        $manager->flush();
    }
}
