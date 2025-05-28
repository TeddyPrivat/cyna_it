<?php

namespace App\Controller;

use App\Entity\Command;
use App\Repository\CartRepository;
use App\Repository\CommandRepository;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class CommandController extends AbstractController
{
    public function __construct(
        private readonly CommandRepository $commandRepository,
        private readonly CartRepository $cartRepository,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository $productRepository,
        private readonly ServiceRepository $serviceRepository
    ) {}

    #[Route('/commands', name: 'app_commands', methods: ['GET'])]
    public function commandes(): JsonResponse
    {
        $commands = $this->commandRepository->findAll();
        $data = [];

        foreach ($commands as $command) {
            $data[] = [
                'id' => $command->getId(),
                'product' => $command->getProduct()?->getId(),
                'service' => $command->getService()?->getId(),
                'quantity' => $command->getQuantity(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/commands/{user_id}', name: 'app_command', methods: ['GET'])]
    public function command(int $user_id): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $commandItems = $this->commandRepository->findBy(['user' => $user]);

        $commandData = [];

        foreach ($commandItems as $item) {
            $commandData[] = [
                'id' => $item->getId(),
                'product' => $item->getProduct()?->getId(),
                'service' => $item->getService()?->getId(),
                'quantity' => $item->getQuantity(),
            ];
        }

        return new JsonResponse($commandData);
    }

    #[Route('/command/create/{user_id}', name: 'app_command_create', methods: ['POST'])]
    public function createCommandFromCart(int $user_id): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $cartItems = $this->cartRepository->findBy(['user' => $user]);
        if (!$cartItems) {
            return new JsonResponse(['error' => 'Cart is empty'], Response::HTTP_BAD_REQUEST);
        }

        // On cherche le dernier commandId (pas id unique) utilisé
        $lastCommand = $this->commandRepository->findOneBy([], ['commandId' => 'DESC']);
        $newCommandId = $lastCommand ? $lastCommand->getCommandId() + 1 : 1;

        foreach ($cartItems as $cartItem) {
            $command = new Command();
            $command->setCommandId($newCommandId); // <- ici !
            $command->setUser($user);
            $command->setProduct($cartItem->getProduct());
            $command->setService($cartItem->getService());
            $command->setQuantity($cartItem->getQuantity());

            // Update du stock
            $product = $cartItem->getProduct();
            if ($product) {
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
            'command_id' => $newCommandId // <- on retourne le groupement
        ], Response::HTTP_CREATED);
    }

}
