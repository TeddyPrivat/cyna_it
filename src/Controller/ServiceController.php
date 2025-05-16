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
    public function getAllServices(ServiceRepository $sr): JsonResponse
    {
        $services = $sr->findAll();
        $data = [];
        foreach($services as $service){
            $data[] = [
                'id' => $service->getId(),
                'title' => $service->getTitle(),
                'description' => $service->getDescription(),
                'price' => $service->getPrice()
                //Catégorie à ajouter
            ];
        }
        return $this->json($data);
    }

    #[Route('/service/{id}', name:'app_service')]
    public function getServiceById(ServiceRepository $sr, $id): JsonResponse
    {
        $service = $sr->find($id);
        return $this->json($service);
    }
}
