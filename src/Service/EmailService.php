<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService {
    private $mailer;

    public function __construct(MailerInterface $monmailer)
    {
        // stocker dans la propriété $mailer un
        // Mailerinterface
        $this->mailer=$monmailer;
    }

    public function envoyer(
        $email,
        $message){

        $email = (new Email())
        ->from($email)
        ->to($email)
        //->cc('cc@example.com')
        //->bcc('bcc@example.com')
        //->replyTo('fabien@example.com')
        //->priority(Email::PRIORITY_HIGH)
        ->subject('Réclamation')
        ->text($message)
        ->html($message);

        $this->mailer->send($email);
       
 
    }
}

// l'objectif de la classe de se service c'est 
// d'envoyé un mail
// peu importe comment il se débrouille
// si il a besoin d'un mailerinterface
// d'un objet Email
// lors de l'appel je lui envoie juste
//  $emailService->envoyer(
//    $email_form, // l'email
//    $data['Votre_message']); // et le message

     


// return : lorsqu'on a besoin de recuperer une info.

// lorsque l' EmailService est instancié
// il est instancié au dans les parentheses du controlleur
// dans la page contact
// lors de l'instanciation
// on a la propriété mailer qui va recevoir
//  le mailerinterface.
// l'avantage c'est que je n'ai pas besoin de l'appeller
// lors de l'appel du service.
// ca se fait seul lors de l'instanciation
// c'est transparent.
// i l n'a pas de return parce qu'on objectif n'est pas
// de recuperer un infos

// on aurait pu imaginer de renvoyer un code erreur
// par exemple si il y a une erreur lors de l'envoie
// du mail on renvoie 1 si non 0