<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class UserController extends AbstractController
{
    public function __construct(private readonly UserService $userService) {}

    // Route pour récupérer tous les utilisateurs
    #[Route('/users', name: 'app_user_list', methods: ['GET'])]
    public function getAllUsers(): Response
    {
        $data = $this->userService->getAllUsersData();
        return $this->json($data);
    }

    // Route pour récupérer un utilisateur par son ID
    #[Route('/users/{id}', name: 'app_user_show', methods: ['GET'])]
    public function getOneUser(UserRepository $ur, int $id): Response
    {
        $user = $ur->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $data = [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'role' => $user->getRoles()
        ];

        return $this->json($data);
    }
}
