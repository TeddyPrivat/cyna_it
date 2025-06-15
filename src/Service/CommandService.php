<?php

namespace App\Service;

use App\Entity\Command;
use App\Repository\CartRepository;
use App\Repository\CommandRepository;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CommandService
{
    public function __construct(
        private readonly CommandRepository $commandRepository,
        private readonly CartRepository $cartRepository,
        private readonly UserRepository $userRepository,
        private readonly ProductRepository $productRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function getAllCommands(): array
    {
        $commands = $this->commandRepository->findAll();
        return array_map(fn($command) => [
            'id' => $command->getId(),
            'product' => $command->getProduct()?->getId(),
            'service' => $command->getService()?->getId(),
            'quantity' => $command->getQuantity(),
        ], $commands);
    }

    public function getCommandsForUser($user): array
    {
        $commands = $this->commandRepository->findBy(['user' => $user]);
        return array_map(fn($command) => [
            'id' => $command->getId(),
            'product' => $command->getProduct()?->getId(),
            'service' => $command->getService()?->getId(),
            'quantity' => $command->getQuantity(),
        ], $commands);
    }

    public function createCommandFromCart(int $user_id): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $cartItems = $this->cartRepository->findBy(['user' => $user]);
        if (empty($cartItems)) {
            return new JsonResponse(['error' => 'Cart is empty'], Response::HTTP_BAD_REQUEST);
        }

        $lastCommand = $this->commandRepository->findOneBy([], ['commandId' => 'DESC']);
        $newCommandId = $lastCommand ? $lastCommand->getCommandId() + 1 : 1;

        foreach ($cartItems as $cartItem) {
            $command = new Command();
            $command->setCommandId($newCommandId);
            $command->setUser($user);
            $command->setProduct($cartItem->getProduct());
            $command->setService($cartItem->getService());
            $command->setQuantity($cartItem->getQuantity());

            if ($product = $cartItem->getProduct()) {
                $product->setStock($product->getStock() - $cartItem->getQuantity());
                $this->entityManager->persist($product);
            }

            $this->entityManager->persist($command);
        }

        foreach ($cartItems as $cartItem) {
            $this->entityManager->remove($cartItem);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Command created successfully',
            'command_id' => $newCommandId,
        ], Response::HTTP_CREATED);
    }
}
