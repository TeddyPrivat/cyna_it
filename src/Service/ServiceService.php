<?php

namespace App\Service;

use App\Repository\ServiceRepository;
use App\Entity\Service;

class ServiceService
{
    public function __construct(private readonly ServiceRepository $serviceRepository) {}

    public function getAllServices(): array
    {
        $services = $this->serviceRepository->findAll();
        $data = [];

        foreach ($services as $service) {
            $data[] = [
                'id' => $service->getId(),
                'title' => $service->getTitle(),
                'description' => $service->getDescription(),
                'price' => $service->getPrice(),
                'categories' => $service->getCategories(),
            ];
        }

        return $data;
    }

    public function getServiceById(int $id): ?array
    {
        $service = $this->serviceRepository->find($id);

        if (!$service) {
            return null;
        }

        return [
            'id' => $service->getId(),
            'title' => $service->getTitle(),
            'description' => $service->getDescription(),
            'price' => $service->getPrice(),
            'image' => $service->getImage(),
            'categories' => $service->getCategories()
        ];
    }

    public function createService(array $data): array
    {
        $service = new Service();
        $service->setName($data['title']);
        $service->setDescription($data['description']);
        $service->setPrice($data['price']);
        $service->setCategories($data['categories'] ?? []);

        $this->serviceRepository->save($service);

        return [
            'id' => $service->getId(),
            'title' => $service->getTitle(),
            'description' => $service->getDescription(),
            'price' => $service->getPrice(),
            'categories' => $service->getCategories()
        ];
    }

    public function updateService(int $id, array $data): ?array
    {
        $service = $this->serviceRepository->find($id);
        
        if (!$service) {
            return null;
        }

        $service->setName($data['title'] ?? $service->getTitle());
        $service->setDescription($data['description'] ?? $service->getDescription());
        $service->setPrice($data['price'] ?? $service->getPrice());
        $service->setCategories($data['categories'] ?? $service->getCategories());

        $this->serviceRepository->save($service);

        return [
            'id' => $service->getId(),
            'title' => $service->getTitle(),
            'description' => $service->getDescription(),
            'price' => $service->getPrice(),
            'categories' => $service->getCategories()
        ];
    }

    public function deleteService(int $id): bool
    {
        $service = $this->serviceRepository->find($id);
        
        if (!$service) {
            return false;
        }

        $this->serviceRepository->remove($service);
        return true;
    }
}