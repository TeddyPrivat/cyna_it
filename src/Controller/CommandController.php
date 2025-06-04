<?php

namespace App\Controller;

use App\Service\CommandService;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class CommandController extends AbstractController
{
    public function __construct(
        private readonly CommandService $commandService,
        private readonly UserRepository $userRepository
    ) {}

    #[Route('/commands', name: 'app_commands', methods: ['GET'])]
    public function commandes(): JsonResponse
    {
        return $this->json($this->commandService->getAllCommands());
    }

    #[Route('/commands/{user_id}', name: 'app_command', methods: ['GET'])]
    public function command(int $user_id): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->commandService->getCommandsForUser($user));
    }

    #[Route('/command/create/{user_id}', name: 'app_command_create', methods: ['POST'])]
    public function createCommandFromCart(int $user_id): JsonResponse
    {
        return $this->commandService->createCommandFromCart($user_id);
    }
}
