<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Order;
use App\Service\Cart;
use App\Entity\Produit;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session/{reference}', name: 'app_stripe_create_session')]
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference): Response
    {
        $products_for_stripe = [];

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);

        if (!$order) {
            new JsonResponse(['error' => 'order']);
        }
        

        foreach ($order->getOrderDetails()->getValues() as $product) { 
            $product_object = $entityManager->getRepository(Produit::class)->findOneByTitre($product->getProduit());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrix(),
                    'product_data' => [
                        'name' => $product->getProduit(),
                        //'images' => [$YOUR_DOMAIN."uploads/".$product['product']->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantite(),
            ];
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getTransportPrix(),
                'product_data' => [
                    'name' => $order->getTransportNom(),
                    //'images' => [$YOUR_DOMAIN."uploads/".$product['product']->getIllustration()],
                ],
            ],
            'quantity' => 1,
        ];

     
         // On va instancier la class api Stripe et de setter ApiKey
        $stripeSecretKey=('sk_test_51MrGA8KHSNIzy75p7jDEVtxlokqPub18B94vO53CVcEi1ePS05PVTQ9a8nZSNsFosdtJ7KWpsYOsQ1dwIDfcgOw7006nT95rHM');
        Stripe::setApiKey($stripeSecretKey);

        // On définit le protocole de connexion http ou https avec les variable global PHP
        // et le nom du serveur de sorte de pouvoir gérer tout les enviroments possible
        $protocol="http://";

            if (isset($_SERVEUR['HTTPS'])){
                $protocol="https://";
            }

        $serveur=$_SERVER['SERVER_NAME'];
        $YOUR_DOMAIN=$protocol.$serveur;

        $checkout_session = Session::create([ // 1/ La checkout_session = Session::create va générer un ID de session pour stripe
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [
                $products_for_stripe
           ],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}', // 3/On la passe en paramètre d'URL, comme ça sur notre vue 'merci' dans le controleur
                'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}', // on va pouvoir retrouver notre commande avec notre checkout_session_id qui sera automatiquement remplacer par stripe
                // 4/ Aller stocker le checkout_session_id dans l'entité Order en faisant une ligne de commande dans le terminal on va aller ajouter une nouvelle propriété stripeSessionID
        ]);

        $order->setStripeSessionID($checkout_session->id);
        $entityManager->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]); // 2/génère un ID pour stripe
        return $response;
    }
}
