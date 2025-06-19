<?php

namespace App\Controller;

use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
final class ProductController extends AbstractController
{
    public function __construct(private readonly ProductService $productService) {}

    #[Route('/products', name: 'app_products', methods: ['GET'])]
    public function getAllProducts(): JsonResponse
    {
        $products = $this->productService->getAllProducts();
        return $this->json($products);
    }


    #[Route('/product/{id}', name: 'app_product', methods: ['GET'])]
    public function getProductById(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($product);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/product', name: 'app_create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $product = $this->productService->createProduct($data);
        return $this->json($product, Response::HTTP_CREATED);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/product/{id}', name: 'app_update_product', methods: ['PUT', 'PATCH'])]
    public function updateProduct(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $product = $this->productService->updateProduct($id, $data);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($product);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/product/{id}', name: 'app_delete_product', methods: ['DELETE'])]
    public function deleteProduct(int $id): JsonResponse
    {
        $deleted = $this->productService->deleteProduct($id);

        if (!$deleted) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['message' => 'Product deleted successfully']);
    }
}
