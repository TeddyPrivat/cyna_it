<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $CategoryRepository,
    ) {}

    #[Route('/categories', name: 'app_categories', methods: ['GET'])]
    public function categories(): JsonResponse
    {
        $categories = $this->CategoryRepository->findAll();
        $data = [];

        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'categoryName' => $category->getCategoryName(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/category/{id}', name: 'app_category', methods: ['GET'])]
    public function category($id): JsonResponse
    {
        $category = $this->CategoryRepository->find($id);

        if (!$category) {
            return new JsonResponse(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $category->getId(),
            'categoryName' => $category->getCategoryName(),
        ]);
    }

}
