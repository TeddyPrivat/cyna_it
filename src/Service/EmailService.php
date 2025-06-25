<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private readonly UrlGeneratorInterface $urlGenerator,
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

    public function sendValidationLink(string $email, string $token, int $id): void
    {
        try {
//            $url = $this->urlGenerator->generate(
//                'app_validate_user',
//                ['id' => $id, 'token' => $token],
//                UrlGeneratorInterface::ABSOLUTE_URL
//            );
            $url = "http://localhost:5173/validate/user/" . $token;

            $message = (new Email())
                ->from('no-reply@monsite.com')
                ->to($email)
                ->subject('Validez votre adresse email')
                ->html("
                <h2>Bienvenue à Cyna IT</h2>
                <p>Merci de vous être inscrit. Veuillez cliquer sur le bouton ci-dessous pour confirmer votre adresse email :</p>
                <p>
                    <a href=\"$url\" style=\"display:inline-block;padding:10px 20px;background:#007BFF;color:white;text-decoration:none;border-radius:5px;\">
                        Confirmer mon adresse
                    </a>
                </p>
                <p>Ou copiez-collez ce lien dans votre navigateur :</p>
                <p><code>$url</code></p>
                <hr>
                <small>Ce lien expirera dans 30 minutes.</small>
            ");

            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Erreur lors de l’envoi de l’email : ' . $e->getMessage());
        }
    }

}