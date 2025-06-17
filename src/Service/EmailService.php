<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private string $mailerFrom
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function sendMail(string $to, string $from, string $subject): void
    {
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->html("Le test fonctionne bien.");

        $this->mailer->send($email);
    }
    public function sendRecoverPasswordMail(string $to, string $subject, string $password): void
    {
        $email = (new Email())
            ->from($this->mailerFrom)
            ->to($to)
            ->subject($subject)
            ->html("
            Bonjour,\n\nVoici votre nouveau mot de passe : $password\n\nMerci de le modifier après connexion.
            ");
        $this->mailer->send($email);
    }
}
