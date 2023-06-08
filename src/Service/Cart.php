<?php

namespace App\Service;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart // cette class Cart permet de gérer l'ensemble des opérations lié à notre panier 
{
    private $session;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();//Dans le constructeur, on récupère directement la session avec $this->session = $requestStack->getSession();
        $this->entityManager = $entityManager;
    }

    public function add($id)
    { // Dans la méthode add(), on utilise `isset()` pour tester si l'élément `$cart[$id]` existe, au lieu de `empty()`
        $cart = $this->session->get('cart', []);

        if (isset($cart[$id])) { // Ensuite, si l'élément existe, on incrémente sa valeur, sinon on le met à 1.             
            $cart[$id]++;   
        } else {
            $cart[$id] = 1; // j'ai besoin que tu me prennes mon panier ($cart[$id]) et que tu me fasses 1
        }
        $this->session->set('cart', $cart);
    }


    public function get() // Dans la méthode get(), on renvoie un tableau vide par défaut si le panier n'existe pas encore dans la session.
    {
        return $this->session->get('cart', []);
    }

    public function remove() // Remove pour vider le panier
    {
        return $this->session->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }

    public function decrease($id)
        // vérifier si la quantité de notre produit n'est pas = à 1
        // parceque si elle est = à 1 à ce moment là ce n'est plus une diminution de 1 que l'on souhaite
        // faire mais c'est la suppression du produit
    {
        $cart = $this->session->get('cart', []);

        if ($cart[$id] > 1) {
            $cart[$id]--; // retirer une quantité (-1)
            
        } else {
            unset($cart[$id]);// Supprimer mon produit
        }
        return $this->session->set('cart', $cart);  
    }

    public function getFull()
    {
        $cartComplete = [];

        foreach ($this->get() as $id => $quantity) {
            $product_object = $this->entityManager->getRepository(Produit::class)->findOneById($id);

            if (!$product_object) { // code de securité  pour éviter qu'un utilisateur ajoute dans le panier des produits qui n'existent pas
                $this->delete($id);
                continue;// et qu'il sorte de la boucle foreach et qu'il passe au produit suivant et cela évitera d'affecter le produit à $cartComplete
            }

            $cartComplete[] = [
                'product' => $product_object,
                'quantity' => $quantity
            ];
        } 
        return $cartComplete;
    }
    
}