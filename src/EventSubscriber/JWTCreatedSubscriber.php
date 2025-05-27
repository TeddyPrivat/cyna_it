<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTCreatedSubscriber implements EventSubscriberInterface
{
    public function onLexikJwtAuthenticationOnJwtCreated($event): void
    {
        // ...
        $user = $event->getUser();
        $payload = $event->getData();

//        $payload['nom'] = $user->getLastname();
//        $payload['prenom'] = $user->getFirstname();
        $payload['id'] = $user->getId();

        $event->setData($payload);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnJwtCreated',
        ];
    }
}
