<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            $data[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'imgUrl' => $product->getImgUrl(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                //Catégorie sera à rajouter
            ];
        }
        return $this->json($data);
    }

    #[Route('/product/{id}', name: 'app_product', methods: ['GET'])]
    public function getProductById(ProductRepository $pr, $id): JsonResponse
    {
        $product = $pr->find($id);
        return $this->json($product);
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
}
