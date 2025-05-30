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
}
