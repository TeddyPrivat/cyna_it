<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $em
    ) {}

    public function getAllUsersData(): array
    {
        $users = $this->userRepository->findAll();
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'adress' => $user->getAdress(),
                'postalCode' => $user->getPostalCode(),
                'city' => $user->getCity(),
            ];
        }

        return $data;
    }
    public function getUserById(int $id): ?array
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return null;
        }

        return [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'adress' => $user->getAdress(),
            'postalCode' => $user->getPostalCode(),
            'city' => $user->getCity(),
        ];
    }
    public function getUserByEmail(string $email): ?array
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return null;
        }
        return [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'adress' => $user->getAdress(),
            'postalCode' => $user->getPostalCode(),
            'city' => $user->getCity(),
        ];
    }
    public function resetPassword(array $userData): ?array
    {
        $id = $userData['id'];
        $user = $this->userRepository->find($id);

        if (!$user) {
            return null;
        }

        $newPassword = bin2hex(random_bytes(4)); // 8 caractères aléatoires
        if ($this->changePassword($user, $newPassword)) {
            return ['new_password' => $newPassword];
        }

        return null;
    }

    private function changePassword(User $user, string $plainPassword): bool
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        try {
            $this->em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
