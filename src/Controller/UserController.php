<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\EmailService;

#[Route('/api')]
final class UserController extends AbstractController
{
    public function __construct(private readonly UserService $userService) {}

    #[Route('/users', name: 'app_user_list', methods: ['GET'])]
    public function getAllUsers(): Response
    {
        $data = $this->userService->getAllUsersData();
        return $this->json($data);
    }

    #[Route('/users/{id}', name: 'app_user_show', methods: ['GET'])]
    public function getOneUser(int $id): Response
    {
        $data = $this->userService->getUserById($id);

        if (!$data) {
            return $this->json(['error' => 'User not found'], 404);
        }

        return $this->json($data);
    }
    #[Route('/forgot-password', name: 'app_user_forgot_password', methods: ['POST'])]
    public function forgotPassword(Request $request, EmailService $emailService): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Data is empty'], 400);
        }

        if (empty($data['email'])) {
            return $this->json(['error' => 'Email is required'], 400);
        }

        $email = $data['email'];
        $user = $this->userService->getUserByEmail($email);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $result = $this->userService->resetPassword($user);
        if (!$result || empty($result['new_password'])) {
            return $this->json(['error' => 'Password reset failed'], 500);
        }

        $newPassword = $result['new_password'];
        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Adresse email invalide'], 400);
        }

        try {
            $emailService->sendRecoverPasswordMail(
                "{$user['email']}",
                "Mot de passe oubliée",
                "{$newPassword}"

            );
        } catch (\Throwable $e) {
            return $this->json(['error' => sprintf('Error sending email to %s', $user['email']), 'details' => $e->getMessage()], 500);
        }

        return $this->json(['message' => 'Password reset successfully', 'details' => $newPassword]);
    }
    #[Route('/users/{id}', name: 'app_user_update', methods: ['PUT'])]
    public function updateUser(int $id, Request $request): Response
    {
        // On utilise le service UserService pour mettre à jour l'utilisateur (function updateUserData)
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Data is empty'], 400);
        }
        $data['id'] = $id; // Ajout de l'ID à la donnée pour la mise à jour
        $user = $this->userService->updateUserData($data);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }
    }
}
