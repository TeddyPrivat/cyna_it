<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository
    ) {}

    public function getAllProducts(): array
    {
        $products = $this->productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = $this->serializeProduct($product);
        }

        return $data;
    }

    public function getProductById(int $id): ?array
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return null;
        }

        return $this->serializeProduct($product);
    }

    public function createProduct(array $data): array
    {
        $product = new Product();
        $product->setTitle($data['title']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setImgUrl($data['imgUrl'] ?? null);
        $product->setStock($data['stock'] ?? 0);

        if (!empty($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $categoryId) {
                $category = $this->categoryRepository->find($categoryId);
                if ($category) {
                    $product->addCategory($category);
                }
            }
        }

        $this->productRepository->save($product, true); // flush = true

        return $this->serializeProduct($product);
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
        if (isset($data['stock'])) {
            $product->setStock($data['stock']);
        }

        if (isset($data['categories']) && is_array($data['categories'])) {
            // Réinitialiser la collection actuelle
            foreach ($product->getCategories() as $existingCategory) {
                $product->removeCategory($existingCategory);
            }

            foreach ($data['categories'] as $categoryId) {
                $category = $this->categoryRepository->find($categoryId);
                if ($category) {
                    $product->addCategory($category);
                }
            }
        }

        $this->productRepository->save($product, true); // flush = true

        return $this->serializeProduct($product);
    }

    public function deleteProduct(int $id): bool
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return false;
        }

        $this->productRepository->remove($product, true); // flush = true
        return true;
    }

    private function serializeProduct(Product $product): array
    {
        $categories = [];
        foreach ($product->getCategories() as $category) {
            $categories[] = [
                'id' => $category->getId(),
                'categoryName' => $category->getCategoryName(),
            ];
        }

        return [
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'imgUrl' => $product->getImgUrl(),
            'stock' => $product->getStock(),
            'categories' => $categories,
        ];
    }
}
