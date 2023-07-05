<?php

namespace App\Controller;

use App\Entity\Header;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccueilController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/', name: 'app_accueil')] 

    public function index(): Response
    {
        $produits = $this->entityManager->getRepository(Produit::class)->findByIsBest(1); 

        $headers = $this->entityManager->getRepository(Header::class)->findAll();
        //dd($produits);
        return $this->render('accueil/index.html.twig', [ 
           'produits_best' => $produits,

           'headers' => $headers 
        ]);
    }
}
