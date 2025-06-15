<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Entity\User;

class UserService
{
    public function __construct(private readonly UserRepository $userRepository) {}

    public function getAllUsersData(): array
    {
        $users = $this->userRepository->findAll();
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail()
            ];
        }

        return $data;
    }
}
