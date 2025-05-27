<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api')]
final class SignUpController extends AbstractController
{
    #[Route('/signup', name: 'app_signup', methods: ['POST'])]
    public function signUp(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Invalid JSON.'], 400);
        }

        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;
        $adress = $data['adress'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $confirmPassword = $data['confirm_password'] ?? null;

//        if (!$firstname || !$lastname || !$email || !$password || !$confirmPassword) {
//            return $this->json(['error' => 'Missing required fields.'], 400);
//        }

        if ($password !== $confirmPassword) {
            return $this->json(['error' => 'Passwords do not match.'], 400);
        }

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return $this->json(['error' => 'User already exists. Please log in.'], 409);
        }

        $user = new User();
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setAdress($adress);
        $user->setRole('ROLE_USER');

        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => 'User created successfully.',
            'userId' => $user->getId(),
        ], 201);
    }
}
