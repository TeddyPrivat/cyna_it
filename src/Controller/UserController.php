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

    #[Route('/users', name: 'app_user', methods: ['GET'])]
    public function getAllUsers(): Response
    {
        $data = $this->userService->getAllUsersData();
        return $this->json($data);
    }
//   get user by id
    #[Route('/users/{id}', name: 'app_user', methods: ['GET'])]
    public function getOneUser(UserRepository $ur, int $id): Response
    {
        $user = $ur->find($id);
        $data = [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ];
        return $this->json($data);
    }
}
