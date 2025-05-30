<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Entity\Product;

class ProductService
{
    public function __construct(private readonly ProductRepository $productRepository) {}

    public function getAllProducts(): array
    {
        $products = $this->productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'imgUrl' => $product->getImgUrl(),
                'categories' => $product->getCategories()
            ];
        }

        return $data;
    }

    public function getProductById(int $id): ?array
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return null;
        }

        return [
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'imgUrl' => $product->getImgUrl(),
            'categories' => $product->getCategories()
        ];
    }

    public function createProduct(array $data): array
    {
        $product = new Product();
        $product->setTitle($data['title']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setImgUrl($data['imgUrl']);
        $product->setCategories($data['categories'] ?? []);

        $this->productRepository->save($product);

        return [
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'imgUrl' => $product->getImgUrl(),
            'categories' => $product->getCategories()
        ];
    }

    public function updateProduct(int $id, array $data): ?array
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return null;
        }

        if (isset($data['title'])) {
            $product->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }
        if (isset($data['imgUrl'])) {
            $product->setImgUrl($data['imgUrl']);
        }
        if (isset($data['categories'])) {
            $product->setCategories($data['categories']);
        }

        $this->productRepository->save($product);

        return [
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'imgUrl' => $product->getImgUrl(),
            'categories' => $product->getCategories()
        ];
    }

    public function deleteProduct(int $id): bool
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return false;
        }

        $this->productRepository->remove($product);
        return true;
    }
}