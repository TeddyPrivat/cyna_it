<?php

namespace App\Service;

use App\Entity\SupportMessage;
use App\Repository\SupportMessageRepository;

readonly class SupportMessageService
{
    public function __construct(private SupportMessageRepository $smr){ }

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

    /**
     * @throws \Exception
     */
    public function deleteSupportMessage($id): void
    {
        $deleteMessage = $this->smr->find($id);
        if(!$deleteMessage){
            throw new \Exception("Message de support non trouvé pour l'ID $id.");
        }
        $this->smr->remove($deleteMessage, true);
    }

    public function serializeSupportMessage(SupportMessage $supportMessage): array
    {
        return [
            'id' => $supportMessage->getId(),
            'firstname' => $supportMessage->getFirstName(),
            'lastname' => $supportMessage->getLastName(),
            'email' => $supportMessage->getEmail(),
            'message' => $supportMessage->getMessage(),
        ];
    }
}