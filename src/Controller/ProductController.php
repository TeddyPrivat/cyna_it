<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_products', methods: ['GET'])]
    public function products(ProductRepository $pr): Response
    {
        $products = $pr->findAll();
        $data = [];
        foreach($products as $product){
            $categories = [];
            foreach ($product->getCategories() as $category) {
                $categories[] = $category->getCategoryName();
            }
            $data[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'imgUrl' => $product->getImgUrl(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                'categories' => $categories
            ];
        }
        return $this->json($data);
    }

    #[Route('/product/{id}', name: 'app_product', methods: ['GET'])]
    public function getProductById(ProductRepository $pr, $id): JsonResponse
    {
        $product = $pr->find($id);
        if (!$product) {
            return $this->json(['error' => 'Produit non trouvé'], 404);
        }
        return $this->json($product, 200, [], ['groups' => 'product:read']);
    }

    #[Route('/product/delete/{id}', name: 'app_delete_product', methods: ['POST'])]
    public function deleteProductById(ProductRepository $pr, $id, EntityManagerInterface $em): JsonResponse
    {
        $product = $pr->find($id);
        if (!$product) {
            return $this->json(['error' => 'Produit introuvable'], 404);
        }else{
            $em->remove($product);
            $em->flush();
            return $this->json(['message' => "Le produit a bien été supprimé"]);
        }
    }

    #[Route('/product/add', name: 'app_add_product', methods: ['POST'])]
    public function addProduct(EntityManagerInterface $em, Request $request, CategoryRepository $cr): JsonResponse{
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $title = $data['title'];
        $description = $data['description'];
        $price = $data['price'];
        $stock = $data['stock'];
        $categories = $data['categories'];

        if (!$title || !$description || $price === null || $stock === null) {
            return new JsonResponse(['error' => 'Missing fields'], 400);
        }
        $product = new Product();
        $product->setTitle($title);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setStock($stock);
        $product->setImgUrl("");
        foreach ($data['categories'] as $catData) {
            $category = $cr->find($catData['id']);
            if ($category) {
                $product->addCategory($category);
            }
        }

        $em->persist($product);
        $em->flush();

        return new JsonResponse([
            'message' => 'Produit ajouté avec succès',
            'product' => [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                'categories' => $product->getCategories()
            ]
        ], 201);
    }

    #[Route('/product/edit/{id}', name: 'app_edit_product', methods: ['PUT'])]
    public function modifyProduct(int $id, ProductRepository $pr, EntityManagerInterface $em, Request $request, CategoryRepository $cr): JsonResponse
    {
        $product = $pr->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $product->setTitle($data['title'] ?? $product->getTitle());
        $product->setDescription($data['description'] ?? $product->getDescription());
        $product->setPrice($data['price'] ?? $product->getPrice());
        $product->setStock($data['stock'] ?? $product->getStock());

        //ici on vide ce que contient le produit pour les catégories, car sinon les changements ne sont pas vraiment pris en compte
        //on récupère les catégories existantes et on en rajoute seulement, en les vidant de base on évite ce problème
        foreach ($product->getCategories() as $existingCategory) {
            $product->removeCategory($existingCategory);
        }
        //on les remplit avec les nouvelles données
        foreach ($data['categories'] as $catData) {
            $category = $cr->find($catData['id']);
            if ($category) {
                $product->addCategory($category);
            }
        }

        $em->persist($product);
        $em->flush();

        return new JsonResponse(['message' => 'Produit mis à jour avec succès'], 200);

    }
}
