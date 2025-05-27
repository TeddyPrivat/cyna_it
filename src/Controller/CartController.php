<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
final class CartController extends AbstractController
{
    public function __construct(
        private readonly CartRepository $cartRepository,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository $productRepository,
        private readonly ServiceRepository $serviceRepository
    ) {}

    #[Route('/carts', name: 'app_carts', methods: ['GET'])]
    public function carts(): JsonResponse
    {
        $carts = $this->cartRepository->findAll();
        $data = [];

        foreach ($carts as $cart) {
            $data[] = [
                'id' => $cart->getId(),
                'product' => $cart->getProduct(),
                'service' => $cart->getService(),
                'quantity' => $cart->getQuantity(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/cart/{user_id}', name: 'app_cart_user', methods: ['GET'])]
    public function cart(int $user_id): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $cartItems = $this->cartRepository->findBy(['user' => $user]);

        $cartData = [];

        foreach ($cartItems as $item) {
            $cartData[] = [
                'id' => $item->getId(),
                'product' => $item->getProduct(),
                'service' => $item->getService(),
                'quantity' => $item->getQuantity(),
            ];
        }

        return new JsonResponse($cartData);
    }

    #[Route('/cart/{user_id}', name: 'app_cart_add', methods: ['POST'])]
    public function addToCart(Request $request, int $user_id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Récupérer l'utilisateur
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Quantité à ajouter (au moins 1)
        $quantityToAdd = $data['quantity'] ?? 1;
        if ($quantityToAdd < 1) {
            return new JsonResponse(['error' => 'Quantity must be at least 1'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer le produit si fourni
        $product = null;
        if (isset($data['product'])) {
            $product = $this->productRepository->find($data['product']);
            if (!$product) {
                return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
            }
        }

        // Récupérer le service si fourni
        $service = null;
        if (isset($data['service'])) {
            $service = $this->serviceRepository->find($data['service']);
            if (!$service) {
                return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
            }
        }

        // On doit avoir soit un produit soit un service
        if (!$product && !$service) {
            return new JsonResponse(['error' => 'Either product or service must be provided'], Response::HTTP_BAD_REQUEST);
        }

        // Chercher un item existant pour ce user avec le même produit ou service
        $existingItems = $this->cartRepository->findBy(['user' => $user]);

        $foundItem = null;
        foreach ($existingItems as $item) {
            if (
                ($product && $item->getProduct() && $item->getProduct()->getId() === $product->getId()) ||
                ($service && $item->getService() && $item->getService()->getId() === $service->getId())
            ) {
                $foundItem = $item;
                break;
            }
        }

        if ($foundItem) {
            // Incrémenter la quantité existante
            $foundItem->setQuantity($foundItem->getQuantity() + $quantityToAdd);
            $this->cartRepository->save($foundItem, true);
        } else {
            // Créer une nouvelle entrée dans le panier
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setProduct($product);
            $cart->setService($service);
            $cart->setQuantity($quantityToAdd);

            $this->cartRepository->save($cart, true);
        }

        return new JsonResponse(['message' => 'Cart updated successfully'], Response::HTTP_CREATED);
    }



    #[Route('/cart/{user_id}', name: 'app_cart_remove', methods: ['DELETE'])]
    public function removeFromCart(int $user_id, Request $request): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['product']) && !isset($data['service'])) {
            return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $product = null;
        $service = null;

        if (isset($data['product'])) {
            $product = $this->productRepository->find($data['product']);
            if (!$product) {
                return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
            }
        }

        if (isset($data['service'])) {
            $service = $this->serviceRepository->find($data['service']);
            if (!$service) {
                return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
            }
        }

        $cartItems = $this->cartRepository->findBy(['user' => $user]);

        $itemToUpdateOrRemove = null;

        foreach ($cartItems as $item) {
            if (
                ($product && $item->getProduct() === $product) ||
                ($service && $item->getService() === $service)
            ) {
                $itemToUpdateOrRemove = $item;
                break;
            }
        }

        if (!$itemToUpdateOrRemove) {
            return new JsonResponse(['error' => 'Item not found in cart'], Response::HTTP_NOT_FOUND);
        }

        if ($itemToUpdateOrRemove->getQuantity() > 1) {
            $itemToUpdateOrRemove->setQuantity($itemToUpdateOrRemove->getQuantity() - 1);
            $this->entityManager->persist($itemToUpdateOrRemove);
        } else {
            $this->entityManager->remove($itemToUpdateOrRemove);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'cart' => [
                'id' => $itemToUpdateOrRemove->getId(),
                'product' => $itemToUpdateOrRemove->getProduct() ? $itemToUpdateOrRemove->getProduct()->getId() : null,
                'service' => $itemToUpdateOrRemove->getService() ? $itemToUpdateOrRemove->getService()->getId() : null,
                'quantity' => $itemToUpdateOrRemove->getQuantity(),
            ]
        ], Response::HTTP_OK);
    }

}
