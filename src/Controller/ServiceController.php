<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ServiceController extends AbstractController
{
    #[Route('/services', name: 'app_services')]
    public function index(ServiceRepository $sr): JsonResponse
    {
        $services = $sr->findAll();
        $data = [];
        foreach($services as $service){
            $data[] = [
                'id' => $service->getId(),
                'name' => $service->getTitle(),
                'description' => $service->getDescription(),
                'price' => $service->getPrice()
                //Catégorie à ajouter
            ];
        }
        return $this->json($data);
    }
}
