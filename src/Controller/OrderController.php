<?php

namespace App\Controller;


use App\Entity\Order;
use App\Service\Cart;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
// Pour stocker des informations dans la base de données on a besoin de l'entityMaganger de Doctrine
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
       $this->entityManager =  $entityManager; 
    }

    #[Route('/commande', name: 'app_order')]

    public function index(Cart $cart, Request $request): Response
    {
        if (!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('app_account_address_add');
        }
        // Besoin d'initialiser ma variable form en faisant un $this->createForm en lui passant le formulaire que l'on vient de créer OrderType
        $form = $this->createForm(OrderType::class, null, [ // on creer un tableau
            'user' => $this->getUser() // Et on lui passe l'utilisateur en cours et on l'indique au formulaire OrderType
        ]);
        
        return $this->render('order/index.html.twig', [ // Dans le return lui passer la variable form
            'form' => $form->createView(),
            'cart' => $cart->getFull() // on va passer à twig le contenu de notre panier, avec getFull on va récupérer l'objet du produit concerné
        ]);
    }


    #[Route('/commande/recapitulatif', name: 'app_order_recap', methods: ['POST'])] // On rajoute une methode qui dit à symfony tu n'accepte que les requête qui viennent d'un post

    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [ 
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            $date = new DateTimeImmutable();
            $transporteur = $form->get('carriers')->getData();
            $livraison = $form->get('addresses')->getData();
            $livraison_content = $livraison->getPrenom().' '.$livraison->getNom();
            $livraison_content .= '<br/>'.$livraison->getTelephone();

            if ($livraison->getSociete()) {
                $livraison_content .='<br/>'.$livraison->getSociete();
            }

            $livraison_content .='<br/>'.$livraison->getAdresse();
            $livraison_content .='<br/>'.$livraison->getPostal().' '.$livraison->getVille();
            $livraison_content .='<br/>'.$livraison->getPays();
            //dd($livraison_content);

            // ici on va enregister la commande => Entity Order()
            $order = new Order();
            $reference = $date->format('dmY').'-'.uniqid();
            $order->setReference($reference);
            $order->setUtilisateur($this->getUser());
            $order->setCreateAt($date);
            $order->setTransportNom($transporteur->getNom());
            $order->setTransportPrix($transporteur->getPrix());
            $order->setLivraison($livraison_content);
            $order->setIsPaid(0);

            // On dispose donc de l'entity manager pour commencer à stocker les données de mes produits. utilise la variable [persist]
            $this->entityManager->persist($order);

            // Enregistrer les produits => Entity OrderDetails()
            foreach ($cart->getFull() as $product) { // Pour chaque produit que j'ai dans mon panier tu crées un orderDetails
                $orderDetails = new OrderDetails();
                $orderDetails->setCommande($order);
                $orderDetails->setProduit($product['product']->getTitre());
                $orderDetails->setQuantite($product['quantity']);
                $orderDetails->setPrix($product['product']->getPrix());
                $orderDetails->setTotal($product['product']->getPrix() * $product['quantity']);
                $this->entityManager->persist($orderDetails);

            }

            $this->entityManager->flush();

            return $this->render('order/add.html.twig', [ // si l'utilisateur n'arrive pas avec un post dans la commande/recapitulatif le formulaire ne sera pas affiché
                'cart' => $cart->getFull(),
                 'carrier' => $transporteur,
                 'delivery' => $livraison_content,
                 'reference' => $order->getReference()
            ]);
        }
            return $this->redirectToRoute('app_cart'); // mais sera redirigé vers le panier
        
    }
}
