<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Service\Search;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NosProduitsController extends AbstractController
{
 // c'est dans cette fonction que l'on récupère tous les produits en faisant une requête
        // sql (findAll) avec ORM Doctrine et pour cela il faut utiliser l'entityManager

    private $entityManager;
    // On initialise un constructeur (EntityManagerInterface) que l'on stock dans une variable $entityManager)
    public function __construct(EntityManagerInterface $entityManager)
    {   // il faut initialisé $this->entityManager par un mécanisme d'injection de dépendance
        $this->entityManager = $entityManager;
    }

    #[Route('/nos-produits', name: 'app_produits')]

    public function index(Request $request) // Pour traiter une requête d'un formulaire lorqu'il est soumis, on a besoin de la Request
    {
       $search = new Search(); // On intialise notre class Search
       $form = $this->createForm(SearchType::class, $search); // On appelle mon form avec la méthode $this->createForm() qui va avoir besoin
                                                        // de mon formulaire SearchType et en deuxième paramètre une instance de ma class Search ($search )
                                                        // Et on la remet au dessus de la méthode avec la nouvelle instance de Search (new Search) qui sera passé au formulaire
                                                        // Puis à twig 
        $form->handleRequest($request); // C'est la methode qui permet de dire au formulaire d'écouter la requête
        // pour voir si le formumaire a été soumis et on passe la $request

        if ($form->isSubmitted() && $form->isValid()) {
            // Besoin maintenant de dire à Doctine est-ce que tu peux me récupérer mes produits en fonction de mes recherches et
            // pour faire cela on a besoin d'appeler son Repository de produit et le stocker dans la variable $produits et qu'il me retourne
            // mes produits en fonction de ma rechercher. On utilise pas le findAll mais le findWithSearch et on passe en paramètre
            // l'objet recherche ($search). En revanche comme le findWithSearch n'existe pas, on va avoir besoin de créer une nouvelle
            // fonction dans le ProduitRepository.
            // dd($search);

            $produits = $this->entityManager->getRepository(Produit::class)->findWithSearch($search);
        } else {
                 // Avec findAll on peut récupérer tous les produits dans le Repository Produit
            $produits = $this->entityManager->getRepository(Produit::class)->findAll();
        }
        return $this->render('nos_produits/index.html.twig', [
            // il faut passer à notre twig une variable 'produit' qui nous permet d'afficher tous nos produits coté twig
            'produits_tous' => $produits,
            'form' => $form->createView()
        ]);
    }

    #[Route('/produit/{slug}', name: 'app_unproduit')]
    public function show($slug)
     
    {
        $unproduit = $this->entityManager->getRepository(Produit::class)->findOneBySlug($slug);// C'est une requête SQL
        $produits = $this->entityManager->getRepository(Produit::class)->findByIsBest(1);

        if (!$unproduit) { // Si le produit n'existe pas redirige moi vers la page des produits 'app_produits'
            return $this->redirectToRoute('app_produits');
        }

        return $this->render('nos_produits/show.html.twig', [
            'produit' => $unproduit,
            'produits_best' => $produits
    
        ]);
    }
}

