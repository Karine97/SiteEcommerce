<?php

namespace App\Controller;

use App\Service\Cart;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig',[
            'cart' => $cart->getFull()
        ]);
    }

// Page d'ajout de produit
    #[Route('/cart/add/{id}', name: 'app_cart_add')]

    public function add(Cart $cart,$id): Response
    {
        $cart->add($id);
        return $this->redirectToRoute('app_cart');
    }

// page pour vider notre panier
    #[Route('/cart/remove', name: 'app_cart_remove')]

    public function remove(Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('app_produits');
    }

    #[Route('/cart/delete/{id}', name: 'app_cart_delete')]

    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease')]

    public function decrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);

        return $this->redirectToRoute('app_cart');
    }

}
