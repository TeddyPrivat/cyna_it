<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
final class ServiceController extends AbstractController
{
    public function __construct(
        private readonly ServiceRepository $serviceRepository,
        private readonly EntityManagerInterface $em
    ) {}
    #[Route('/services', name: 'app_services', methods: ['GET'])]
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

    #[Route('/service/{id}', name:'app_service', methods: ['GET'])]
    public function getServiceById(ServiceRepository $sr, $id): JsonResponse
    {
        $service = $sr->find($id);
        return $this->json($service);
    }

    #[Route('/service/{id}', name: 'app_service_delete', methods: ['DELETE'])]
    public function deleteService(int $id): JsonResponse
    {
        $service = $this->serviceRepository->find($id);

        if (!$service) {
            return $this->json(['message' => 'service not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($service);
        $this->em->flush();

        return $this->json(['message' => 'service deleted successfully']);
    }

    #[Route('/service', name: 'app_service_create', methods: ['POST'])]
    public function createService(Request $request): JsonResponse
    {
        $title = $request->query->get('title');
        $description = $request->query->get('description');
        $price = $request->query->get('price');

        if (!$title || !$price) {
            return $this->json([
                'message' => 'Missing required fields: title or price'
            ], Response::HTTP_BAD_REQUEST);
        }

        $categoryParam = $request->query->get('category');

        if (is_array($categoryParam)) {
            // Cas : ?category=2&category=3
            $categoryIds = $categoryParam;
        } elseif (is_string($categoryParam)) {
            // Cas : ?category=2,3,4
            $categoryIds = array_filter(array_map('trim', explode(',', $categoryParam)));
        } else {
            $categoryIds = [];
        }

        $service = new Service();
        $service->setTitle($title);
        $service->setDescription($description);
        $service->setPrice((float) $price);
        $service->setCategory($categoryIds);

        $this->serviceRepository->save($service, true);

        return $this->json([
            'message' => 'service created successfully',
            'id' => $service->getId()
        ], Response::HTTP_CREATED);
    }
}
