<?php

namespace App\Controller;

use App\Service\SupportMessageService;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
final class SupportMessageController extends AbstractController
{
    public function  __construct(private readonly SupportMessageService $supportMessageService){ }

    #[Route('/support/message', name: 'app_getAll_support_message', methods: ['GET'])]
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

    /**
     * @throws \Exception
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/support/message/{id}', name: 'app_delete_support_message', methods: ['DELETE'])]
    public function deleteSupportMessage($id): JsonResponse
    {
        $this->supportMessageService->deleteSupportMessage($id);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
