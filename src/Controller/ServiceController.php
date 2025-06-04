<?php

namespace App\Controller;

use App\Service\ServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class ServiceController extends AbstractController
{
    public function __construct(private readonly ServiceService $serviceService) {}

    #[Route('/services', name: 'app_services', methods: ['GET'])]
    public function getAllServices(): JsonResponse
    {
        $services = $this->serviceService->getAllServices();
        return $this->json($services);
    }

    #[Route('/service/{id}', name: 'app_service', methods: ['GET'])]
    public function getServiceById(int $id): JsonResponse
    {
        $service = $this->serviceService->getServiceById($id);

        if (!$service) {
            return $this->json(['message' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($service);
    }

    #[Route('/service', name: 'app_create_service', methods: ['POST'])]
    public function createService(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $service = $this->serviceService->createService($data);
        return $this->json($service, Response::HTTP_CREATED);
    }

    #[Route('/service/{id}', name: 'app_update_service', methods: ['PUT', 'PATCH'])]
    public function updateService(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $service = $this->serviceService->updateService($id, $data);

        if (!$service) {
            return $this->json(['message' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($service);
    }

    #[Route('/service/{id}', name: 'app_delete_service', methods: ['DELETE'])]
    public function deleteService(int $id): JsonResponse
    {
        $deleted = $this->serviceService->deleteService($id);

        if (!$deleted) {
            return $this->json(['message' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['message' => 'Service deleted successfully']);
    }
}
