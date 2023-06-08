<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSucessController extends AbstractController
{
    private $entityManager;// On initialiser notre variable entityManager parce que on aura besoin d'aller chercher la commande 
    // concerné par le stripeSessionId.
    // Ensuite on aura besoin d'interroger doctrine en faisant :

    public function __construct(EntityManagerInterface $entityManager)
    { // Puis on initialise la variable entityManager
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_validate')]
    
    public function index(Cart $cart, $stripeSessionId): Response // On passe le stripeSessionId à la fonction
    { // Ici on va aller récupérer notre commande

        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUtilisateur() != $this->getUser()) {// Si l'order n'existe pas ou si l'order->getUser est différent
            //de l'utilisateur connecté alors il sera rediriger vers l'acceuil
            return $this->redirectToRoute('accueil');
        }
        
        if (!$order->getIsPaid()) {
            // Il va falloir vider la session "cart"
            $cart->remove();

            // On va avoir besoin de modifier le status isPAID de notre commande en mettant 1
            $order->setIsPaid(1);
            $this->entityManager->flush();// La méthode flush permet de s'assurer que toutes les modifications sont correctement enregistrées
                                        // dans la base de données.
           
        }
         // Ensuite envoyer un email à notre client pour lui confirmer sa commande
       
        // On va afficher les informations de la commande de l'utilisateur
        return $this->render('order_sucess/index.html.twig', [
            'order' => $order
        ]);
    }
}
