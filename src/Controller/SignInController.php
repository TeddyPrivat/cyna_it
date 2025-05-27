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
final class SignInController extends AbstractController
{
    #[Route('/signin', name: 'app_signin', methods: ['POST'])]
    public function signIn(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les données envoyées en JSON
        //$data = json_decode($request->getContent(), true);

        $data = [
            'firstname' => $request->query->get('firstname'),
            'lastname' => $request->query->get('lastname'),
            'email' => $request->query->get('email'),
            'password' => $request->query->get('password'),
        ];

        //dd($request);

        // Vérification de la présence des champs requis
        if (
            !isset($data['firstname']) || 
            !isset($data['lastname']) || 
            !isset($data['email']) || 
            !isset($data['password'])
        ) {
            return $this->json(['error' => 'Missing required fields.'], 400);
        }

        // Vérifier si un utilisateur existe déjà avec cet email
        $existingUser = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $data['email']
        ]);

            if ($existingUser) {
                // Rediriger sur la route /login

                return $this->json([
                    'message' => 'User already exists. Please log in.'
                ], 409)
                ->redirectToRoute('app_login');
            }

        // Créer le nouvel utilisateur
        $user = new User();
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']); // ⚠️ À hasher

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => 'User created successfully.'
        ], 201);
    }
}
