<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{
    private $entityManager; // En instanciant la variable  entityManager, nous permet de faire des requêtes 
    // dans Doctrine pour aller chercher les commandes dont on a besoin 

    public function __construct(EntityManagerInterface $entityManager) // On crée notre constructeur et on injecte notre EntityManageurInterface dans la variable $entityManager
    {
        $this->entityManager = $entityManager; // On initialise le tout
    }

    #[Route('/compte/mes-commandes', name: 'app_account_order')]

    public function index(): Response
    {
        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser()); // Je veux que tu affiches les commandes en ordre décroissant et les commandes payées
        // mais comme le findSuccessOrders n'existe pas dans le Repository il va falloir la créer.
        //dd($orders);

        return $this->render('account/order.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/compte/mes-commandes/{reference}', name: 'app_account_order_show')]

    public function show($reference): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference); // Je veux voir les commandes par references 
        //dd($orders);
        if (!$order || $order->getUtilisateur() != $this->getUser()) { // Si l'order n'existe pas ou que l'order->getUtilisateur est différent de l'utilisateur du compte
            return $this->redirectToRoute('app_account');// Alors tu fais une redirection vers Mon compte
        }


        return $this->render('account/order_show.html.twig', [
            'order' => $order
        ]);
    }
}
