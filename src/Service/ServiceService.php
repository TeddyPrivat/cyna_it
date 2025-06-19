<?php

namespace App\Service;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use App\Repository\CategoryRepository;

class ServiceService
{
    public function __construct(
        private readonly ServiceRepository $serviceRepository,
        private readonly CategoryRepository $categoryRepository
    ) {}

    public function getAllServices(): array
    {
        $services = $this->serviceRepository->findAll();
        $data = [];

        foreach ($services as $service) {
            $data[] = $this->serializeService($service);
        }

        return $data;
    }

    public function getServiceById(int $id): ?array
    {
        $service = $this->serviceRepository->find($id);

        if (!$service) {
            return null;
        }

        return $this->serializeService($service);
    }

    public function createService(array $data): array
    {
        $service = new Service();
        $service->setTitle($data['title']);
        $service->setDescription($data['description']);
        $service->setPrice($data['price']);
        $service->setStock($data['stock']);

        if (!empty($data['categories']) && is_array($data['categories'])) {
            foreach ($data['categories'] as $categoryId) {
                $category = $this->categoryRepository->find($categoryId);
                if ($category) {
                    $service->addCategory($category);
                }
            }
        }

        $this->serviceRepository->save($service, true); // flush = true

        return $this->serializeService($service);
    }

    public function updateService(int $id, array $data): ?array
    {
        $service = $this->serviceRepository->find($id);

        if (!$service) {
            return null;
        }

        if (isset($data['title'])) {
            $service->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $service->setDescription($data['description']);
        }
        if (isset($data['price'])) {
            $service->setPrice($data['price']);
        }

        if(isset($data['stock'])){
            $service->setStock($data['stock']);
        }

        if (isset($data['categories']) && is_array($data['categories'])) {
            foreach ($service->getCategories() as $existingCategory) {
                $service->removeCategory($existingCategory);
            }

            foreach ($data['categories'] as $categoryId) {
                $category = $this->categoryRepository->find($categoryId);
                if ($category) {
                    $service->addCategory($category);
                }
            }
        }

        $this->serviceRepository->save($service, true); // flush = true

        return $this->serializeService($service);
    }

    public function deleteService(int $id): bool
    {
        $service = $this->serviceRepository->find($id);

        if (!$service) {
            return false;
        }

        $this->serviceRepository->remove($service, true); // flush = true
        return true;
    }

    private function serializeService(Service $service): array
    {
        $categories = [];
        foreach ($service->getCategories() as $category) {
            $categories[] = [
                'id' => $category->getId(),
                'categoryName' => $category->getCategoryName(),
            ];
        }

        return [
            'id' => $service->getId(),
            'title' => $service->getTitle(),
            'description' => $service->getDescription(),
            'price' => $service->getPrice(),
            'stock' => $service->getStock(),
            'img_url' => $service->getImgUrl(),
            'categories' => $categories,
        ];
    }
}
