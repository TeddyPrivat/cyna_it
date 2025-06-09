<?php

namespace App\Service;

use App\Entity\SupportMessage;
use App\Repository\SupportMessageRepository;

class SupportMessageService
{
    public function __construct(private readonly SupportMessageRepository $smr){ }

    public function getAllSupportMessages(): array
    {
        $messages = $this->smr->findAll();
        $data = [];

        foreach ($messages as $message) {
            $data[] = $this->serializeSupportMessage($message);
        }
        return $data;
    }

    public function addSupportMessage(array $data): array
    {
        $sm = new SupportMessage();
        $sm->setFirstname($data['firstname']);
        $sm->setLastname($data['lastname']);
        $sm->setEmail($data['email']);
        $sm->setMessage($data['message']);

        $this->smr->save($sm, true);
        return $this->serializeSupportMessage($sm);
    }

    public function serializeSupportMessage(SupportMessage $supportMessage): array
    {
        return [
            'id' => $supportMessage->getId(),
            'firstName' => $supportMessage->getFirstName(),
            'lastName' => $supportMessage->getLastName(),
            'email' => $supportMessage->getEmail(),
            'message' => $supportMessage->getMessage(),
        ];
    }
}