<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
                'name' => $product->getTitle(),
                'description' => $product->getDescription(),
                'imgUrl' => $product->getImgUrl(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                //Catégorie sera à rajouter
            ];
        }
        return $this->json($data);
    }
}
