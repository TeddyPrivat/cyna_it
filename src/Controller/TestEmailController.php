<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TestEmailController extends AbstractController
{
    #[Route('/test/email', name: 'app_test_email', methods: ['GET'])]
    public function index(EmailService $emailService): JsonResponse
    {
        try {
            $emailService->sendMail(
                "teddyprivat@hotmail.fr",
                "CECI EST UN TEST",
                "YOUHOU"
            );
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Erreur lors de l’envoi du mail', 'details' => $e->getMessage()], 500);
        }

        return $this->json(['success' => "Le mail a bien été envoyé."]);
    }
}
