<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EmailService;
use App\Service\UserService;
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
        EmailService $emailService,
        UserService $userService
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON.'], 400);
        }

        $result = $userService->createUser($data);

        if (is_string($result)) {
            // Une erreur s'est produite
            return $this->json(['error' => $result], 400);
        }

        $user = $result;
        $email = $user->getEmail();

        $jwtToken = $userService->generateJWTvalidationToken($email);
        if (!$jwtToken) {
            return $this->json(['error' => 'Generating JWT validation token'], 500);
        }

        try {
            $emailService->sendValidationLink($email, $jwtToken, $user->getId());
        } catch (\Throwable $e) {
            return $this->json(['error' => sprintf('Error sending email to %s', $email), 'details' => $e->getMessage()], 500);
        }

        return $this->json([
            'message' => 'User created successfully.',
            'userId' => $user->getId(),
        ], 201);
    }
}