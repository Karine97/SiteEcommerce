<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Service\EmailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/nous-contacter', name: 'app_contact')]

    public function index(EmailService $emailService, MailerInterface $mailer, Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            // Créer une variable  qui est un tableau clé valeur
            //contenant les données envoyées en POST
            $data=$form->getData(); // getData récupère les données
            $email_form=$data['email'];
            $message_form=$data['content'];

            $emailService->envoyer(
            // 
                $email_form, $data['content'], $mailer
            );

            // On va envoyé l'email
            return $this->render('contact/traitement.html.twig', [ 
            ]);

        }
        
        // affichage du formulaire
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
