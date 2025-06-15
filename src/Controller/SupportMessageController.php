<?php

namespace App\Controller;

use App\Service\SupportMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api')]
final class SupportMessageController extends AbstractController
{
    public function  __construct(private readonly SupportMessageService $supportMessageService){ }

    #[Route('/support/message', name: 'app_support_message', methods: ['GET'])]
    public function getAllSupportMessages(): JsonResponse
    {
        $messages = $this->supportMessageService->getAllSupportMessages();
        return $this->json($messages);
    }

    #[Route('/support/message', name: 'app_add_support_message', methods: ['POST'])]
    public function addSupportMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $product = $this->supportMessageService->addSupportMessage($data);
        return $this->json($product, Response::HTTP_CREATED);
    }
}
