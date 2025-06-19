<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/categorie/add', name: 'app_category_add', methods: ['POST'])]
    public function addCategory(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $category = new Category();
        $category->setCategoryName($data['categoryName']);

        $em->persist($category);
        $em->flush();

        return $this->json([
            'id' => $category->getId(),
            'categoryName' => $category->getCategoryName(),
            "message" => "Catégorie crée avec succès",
        ], 201);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/categorie/delete/{id}', name: 'app_category_delete', methods: ['DELETE'])]
    public function deleteCategory($id, EntityManagerInterface $em, CategoryRepository $cr): JsonResponse
    {
        $category = $cr->find($id);
        if (!$category) {
            return new JsonResponse(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($category);
        $em->flush();

        return $this->json(["message" => "Le produit a été supprimé avec succès"]);
    }
}
