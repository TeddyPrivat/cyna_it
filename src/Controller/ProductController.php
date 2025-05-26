<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


#[Route('/api')]
final class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $em
    ) {}

    #[Route('/products', name: 'app_products', methods: ['GET'])]
    public function products(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'imgUrl' => $product->getImgUrl(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                'categories' => $product->getCategories(), // Assurez-vous que getCategories() retourne un tableau
                // À ajouter : catégorie
            ];
        }

        return $this->json($data);
    }

    #[Route('/product/{id}', name: 'app_product', methods: ['GET'])]
    public function getProductById(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'description' => $product->getDescription(),
            'imgUrl' => $product->getImgUrl(),
            'price' => $product->getPrice(),
            'stock' => $product->getStock(),
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function deleteProduct(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($product);
        $this->em->flush();

        return $this->json(['message' => 'Product deleted successfully']);
    }

    #[Route('/product', name: 'app_product_create', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $title = $request->query->get('title');
        $description = $request->query->get('description');
        $imgUrl = $request->query->get('imgUrl');
        $price = $request->query->get('price');
        $stock = $request->query->get('stock');

        if (!$title || !$price || !$stock) {
            return $this->json([
                'message' => 'Missing required fields: title, price or stock'
            ], Response::HTTP_BAD_REQUEST);
        }

        $categoriesParam = $request->query->get('categories');

        if (is_array($categoriesParam)) {
            // Cas : ?categories=2&categories=3
            $categoryIds = $categoriesParam;
        } elseif (is_string($categoriesParam)) {
            // Cas : ?categories=2,3,4
            $categoryIds = array_filter(array_map('trim', explode(',', $categoriesParam)));
        } else {
            $categoryIds = [];
        }

        $product = new Product();
        $product->setTitle($title);
        $product->setDescription($description);
        $product->setImgUrl($imgUrl);
        $product->setPrice((float) $price);
        $product->setStock((int) $stock);
        $product->setCategories($categoryIds);

        $this->productRepository->save($product, true);

        return $this->json([
            'message' => 'Product created successfully',
            'id' => $product->getId()
        ], Response::HTTP_CREATED);
    }


    
}
