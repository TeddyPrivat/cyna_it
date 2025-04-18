<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/api')]
final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function profile(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        $data = $request->query->get('email');

        // Récupérer l'utilisateur avec l'email $data
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $data
        ]);

        return $this->json([
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
        ]);

    }

    #[Route('/profile', name: 'app_profile_update', methods: ['PUT'])]
    public function profileUpdate(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $id = $request->query->get('id');

        if (!$id) {
            return $this->json(['error' => 'Missing user ID.'], 400);
        }

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found.'], 404);
        }

        // Liste des champs modifiables
        $fields = [
            'firstname' => 'setFirstname',
            'lastname' => 'setLastname',
            'email' => 'setEmail',
            'password' => 'setPassword', // À hacher si besoin
            'adress' => 'setAdress',
            'postal_code' => 'setPostalCode',
            'city' => 'setCity',
        ];

        foreach ($fields as $field => $setter) {
            $value = $request->query->get($field);
            if ($value !== null) {
                $user->$setter($value);
            }
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Profile updated successfully.',
            'user' => [
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
            ]
        ]);
    }


}
