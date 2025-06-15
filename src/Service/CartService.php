<?php

namespace App\Service;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CartService
{
    public function __construct(
        private readonly CartRepository $cartRepository,
        private readonly UserRepository $userRepository,
        private readonly ProductRepository $productRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function getAllCarts(): array
    {
        $carts = $this->cartRepository->findAll();
        return array_map(fn($cart) => [
            'id' => $cart->getId(),
            'product' => $cart->getProduct(),
            'service' => $cart->getService(),
            'quantity' => $cart->getQuantity(),
        ], $carts);
    }

    public function getCartForUser($user): array
    {
        $cartItems = $this->cartRepository->findBy(['user' => $user]);
        return array_map(fn($item) => [
            'id' => $item->getId(),
            'product' => $item->getProduct(),
            'service' => $item->getService(),
            'quantity' => $item->getQuantity(),
        ], $cartItems);
    }

    public function addToCart(int $user_id, array $data): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $quantityToAdd = $data['quantity'] ?? 1;
        if ($quantityToAdd < 1) {
            return new JsonResponse(['error' => 'Quantity must be at least 1'], Response::HTTP_BAD_REQUEST);
        }

        $product = null;
        if (isset($data['product'])) {
            $product = $this->productRepository->find($data['product']);
            if (!$product) {
                return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
            }
        }

        $service = null;
        if (isset($data['service'])) {
            $service = $this->serviceRepository->find($data['service']);
            if (!$service) {
                return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
            }
        }

        if (!$product && !$service) {
            return new JsonResponse(['error' => 'Either product or service must be provided'], Response::HTTP_BAD_REQUEST);
        }

        $existingItems = $this->cartRepository->findBy(['user' => $user]);

        foreach ($existingItems as $item) {
            if (
                ($product && $item->getProduct() && $item->getProduct()->getId() === $product->getId()) ||
                ($service && $item->getService() && $item->getService()->getId() === $service->getId())
            ) {
                $item->setQuantity($item->getQuantity() + $quantityToAdd);
                $this->cartRepository->save($item, true);
                return new JsonResponse(['message' => 'Cart updated successfully'], Response::HTTP_CREATED);
            }
        }

        $cart = new Cart();
        $cart->setUser($user);
        $cart->setProduct($product);
        $cart->setService($service);
        $cart->setQuantity($quantityToAdd);
        $this->cartRepository->save($cart, true);

        return new JsonResponse(['message' => 'Cart updated successfully'], Response::HTTP_CREATED);
    }

    public function removeFromCart(int $user_id, array $data): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if (!isset($data['product']) && !isset($data['service'])) {
            return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $product = isset($data['product']) ? $this->productRepository->find($data['product']) : null;
        $service = isset($data['service']) ? $this->serviceRepository->find($data['service']) : null;

        if (isset($data['product']) && !$product) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['service']) && !$service) {
            return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        $cartItems = $this->cartRepository->findBy(['user' => $user]);
        foreach ($cartItems as $item) {
            if (
                ($product && $item->getProduct() === $product) ||
                ($service && $item->getService() === $service)
            ) {
                if ($item->getQuantity() > 1) {
                    $item->setQuantity($item->getQuantity() - 1);
                    $this->entityManager->persist($item);
                } else {
                    $this->entityManager->remove($item);
                }
                $this->entityManager->flush();

                return new JsonResponse([
                    'cart' => [
                        'id' => $item->getId(),
                        'product' => $item->getProduct()?->getId(),
                        'service' => $item->getService()?->getId(),
                        'quantity' => $item->getQuantity(),
                    ]
                ], Response::HTTP_OK);
            }
        }

        return new JsonResponse(['error' => 'Item not found in cart'], Response::HTTP_NOT_FOUND);
    }
}
