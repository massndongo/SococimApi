<?php


namespace App\Service;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;

class UserService
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    public function email($username, $password, $emailFrom, $emailTo)
    {
        $email = (new Email())
            ->from($emailFrom)
            ->to($emailTo)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject("SOCOCIM")
            ->html('<h1>Bienvenue sur la plateforme SOCOCIM</h1><br><h3>Voici vos informations de Connexion</h3><p>Username: <b>'.$username.'</b></p><p>Password:<b>'.$password.'</b></p>');

        return $email;
    }
}