<?php

namespace App\Controller;

use App\Service\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Pour gérer les adresses des utilisateurs on pourrait utiliser les données qui sont dans le Repository
// En allant chercher le $this->EntityManager, créer le __Construct et mettre le EntityManagerInterface,
// $this->EntityManager->getRepository ect...Mais pour aller plus vite on va utiliser la propriété
// de la console User $user->Adresses() qui avec cette methode va nous permettre de récupérer toutes les 
// adresses de l'utilisateur sachant que dans l'entity User, nous avons un nouveau gettteur getAdresses 
// permettant de récupérer toutes mes adresses

class AccountAddressController extends AbstractController
{
    private $entityManager; // On enregistre l'adresse en base de données et pour cela on aura besoin de l'entityManageur de dotrine (base de données) et la fonction __construct.

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/compte/addresses', name: 'app_account_address')]
    public function index(): Response
    {
        //dd($this->getUser()); En allant dans la vue address de twig on va pouvoir directement récupérer l'objet utilisateur
        return $this->render('account/address.html.twig');
    }

    #[Route('/compte/ajouter-une-adresse', name: 'app_account_address_add')]
    public function add(Cart $cart, Request $request): Response
    {
        $address = new Address();//On lui créer l'instance de la class Address

        $form = $this->createForm(AddressType::class, $address);

        // On faire la sousmission du formulaire en utilisant le handleRequest
        $form->handleRequest($request); // puis mettre la requête en injection 

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($address);
            $address->setUtilisateur($this->getUser()); // cette fonction permet de lier l'utilisateur à son adresse, l'utilisateur étant dans l'entité User
            $this->entityManager->persist($address);// Tu me figes la donnée pour qu'elle soit insérée dans mon objet Address.
            $this->entityManager->flush();
            if ($cart->get()) { // Si j'ai des produits dans mon panier à ce moment la
                return $this->redirectToRoute('app_order'); // je veux que tu redirige vers la page order
            } else { // Et s'il n'y a rien redirige vers la page adresse
                return $this->redirectToRoute('app_account_address');// Lorsque l'adresse a été ajouter, on demande que la page soit rediriger vers le compte address
                // Pour l'affichage, il faut retourner dans le fichier address.html.twig
            }
               
        }

        return $this->render('account/address_form.html.twig', [// On passe dans le tableau twig mes variable 'form' et $form->createView()
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/compte/modifier-une-adresse/{id}', name: 'app_account_address_edit')]
    public function edit(Request $request, $id): Response
    {
        //On va aller chercher l'adresse que l'on souhaite modifier
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if (!$address || $address->getUtilisateur() !=$this->getUser()) {
            // Si mon adresse n'existe pas, tu fais une redirection
            return $this->redirectToRoute('app_account_address');
        // Et est-ce que l'adresse que tu as récupéré appartient bien à l'utilisateur ?
        }

        $form = $this->createForm(AddressType::class, $address);
        
       
        // On faire la sousmission du formulaire en utilisant le handleRequest
        $form->handleRequest($request); // puis mettre la requête en injection 

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_account_address');// Lorsque l'adresse a été ajouter, on demande que la page soit rediriger vers le compte address
            // Pour l'affichage, il faut retourner dans le fichier address.html.twig
        }

        return $this->render('account/address_form.html.twig', [// On passe dans le tableau twig mes variable 'form' et $form->createView()
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/compte/supprimer-une-adresse/{id}', name: 'app_account_address_delete')]
    public function delete($id): Response
    {
        //On va aller chercher l'adresse que l'on souhaite supprimer
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        // Si l'adresse existe et que c'est celle de l'utilisateur
        if ($address && $address->getUtilisateur() == $this->getUser()) {
            $this->entityManager->remove($address);// on supprime
            $this->entityManager->flush();
         
        }
            return $this->redirectToRoute('app_account_address');// Lorsque l'adresse a été ajouter, on demande que la page soir rediriger vers le compte address
            // Pour l'affichage, il faut retourner dans le fichier address.html.twig
    }

}
