<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/webhook/stripe', name: 'stripe_webhook', methods: ['POST'])]
    public function handleStripeWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $secret = $_ENV['STRIPE_WEBHOOK_SECRET'];

        try {
            \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET']);
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            // Payload invalide
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Signature invalide
            return new Response('Invalid signature', 400);
        }

        // Pour l'instant, juste loggons ou dumpons l'événement :
        // dump($event); // si tu testes localement avec symfony server
        // ou log l'événement si en prod :
        $this->logger->info('Stripe webhook received', ['event' => $event]);

        return new Response('Webhook handled', 200);
    }
}
