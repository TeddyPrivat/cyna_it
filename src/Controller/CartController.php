<?php

namespace App\Controller;

use App\Service\CartService;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
final class CartController extends AbstractController
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CartRepository $cartRepository,
        private readonly UserRepository $userRepository
    ) {}

    #[Route('/carts', name: 'app_carts', methods: ['GET'])]
    public function carts(): JsonResponse
    {
        return $this->json($this->cartService->getAllCarts());
    }

    #[Route('/cart/{user_id}', name: 'app_cart_user', methods: ['GET'])]
    public function cart(int $user_id): JsonResponse
    {
        $user = $this->userRepository->find($user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(
            $this->cartService->getCartForUser($user),
            200,
            [],
            ['groups' => ['product:read', 'service:read']]
        );

    }

    #[Route('/cart/{user_id}', name: 'app_cart_add', methods: ['POST'])]
    public function addToCart(Request $request, int $user_id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->cartService->addToCart($user_id, $data);
    }

    #[Route('/cart/{user_id}', name: 'app_cart_remove', methods: ['DELETE'])]
    public function removeFromCart(int $user_id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->cartService->removeFromCart($user_id, $data);
    }
}
