<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Data is empty'], 400);
        }

        $data['id'] = $id;

        // Sinon, mise à jour générale de l'utilisateur
        $user = $this->userService->updateUserData($data);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }
        return $this->json($user);
    }
    #[Route('/users/role/{id}', name: 'app_put_user', methods: ['PUT'])]
    public function putUserRole(Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }
        $user = $this->userService->changeRole($id, $data['role']);
        return $this->json($user);
    }

    #[Route('/validate/user/{id}/{token}', name: 'app_validate_user', methods: ['GET'])]
    public function validateUser($id, $token): JsonResponse
    {
        if (!$token){
            return $this->json(['error' => 'Missing validation token'], 400);
        }
        if (!$id){
            return $this->json(['error' => 'Missing id'], 400);
        }
        $user = $this->userService->getUserById($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }
        return $this->userService->validateEmail($user, $token);
    }

    #[Route('/validate/email', name: 'app_validate_user_email', methods: ['POST'])]
    public function validateUserEmail(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Data is empty'], 400);
        }
        $email = $data['email'];
        if (!$email){
            return $this->json(['error' => 'Email is required'], 400);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Adresse email invalide'], 400);
        }
        $jwtToken = $this->userService->generateJWTvalidationToken($email);

        if(!$jwtToken){
            return $this->json(['error' => 'Generating JWT validation token'], 500);
        }
        return $this->json(['token' => $jwtToken]);
    }
}

