<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    private $entityManager;// On initialiser notre variable entityManager parce que on aura besoin d'aller chercher la commande 
    // concerné par le stripeSessionId.
    // Ensuite on aura besoin d'interroger doctrine en faisant :

    public function __construct(EntityManagerInterface $entityManager)
    { // Puis on initialise la variable entityManager
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/erreur/{stripeSessionId}', name: 'app_order_cancel')]

    public function index($stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUtilisateur() != $this->getUser()) {// Si l'order n'existe pas ou si l'order->getUser est différent
            //de l'utilisateur connecté alors il sera rediriger vers l'acceuil
            return $this->redirectToRoute('accueil');
        }

        // Ensuite envoyer un email à notre client pour lui indiquer l'échec de paiement 
        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
