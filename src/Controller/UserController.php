<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class UserController extends AbstractController
{
    public function __construct(private readonly UserService $userService) {}

    #[Route('/users', name: 'app_user_list', methods: ['GET'])]
    public function getAllUsers(): Response
    {
        $data = $this->userService->getAllUsersData();
        return $this->json($data);
    }

    #[Route('/users/{id}', name: 'app_user_show', methods: ['GET'])]
    public function getOneUser(int $id): Response
    {
        $data = $this->userService->getUserById($id);

        if (!$data) {
            return $this->json(['error' => 'User not found'], 404);
        }

        return $this->json($data);
    }
}
