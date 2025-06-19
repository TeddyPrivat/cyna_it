<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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


    // This method is used to update user data
    public function updateUserData(array $userData): ?User
    {
        $id = $userData['id'];
        $user = $this->userRepository->find($id);

        if (!$user) {
            return null;
        }

        // Update user properties
        if (isset($userData['firstname'])) {
            $user->setFirstname($userData['firstname']);
        }
        if (isset($userData['lastname'])) {
            $user->setLastname($userData['lastname']);
        }
        if (isset($userData['email'])) {
            $user->setEmail($userData['email']);
        }
        if (isset($userData['adress'])) {
            $user->setAdress($userData['adress']);
        }
        if (isset($userData['postalCode'])) {
            $user->setPostalCode($userData['postalCode']);
        }
        if (isset($userData['city'])) {
            $user->setCity($userData['city']);
        }

        // If password is provided, hash it and set it
        if (isset($userData['password']) && !empty($userData['password'])) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);
        }

        try {
            $this->em->flush();
            return $user;
        } catch (\Exception $e) {
            return null;
        }
    }

    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function changeRole($id, $role): array
    {
        $user = $this->userRepository->find($id);
        $user->setRoles($role);

        $this->em->persist($user);
        $this->em->flush();
        return $this->serializeUser($user);
    }

    private function serializeUser(User $user): array
    {

        return [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'role' => $user->getRoles(),
        ];

    }
}
