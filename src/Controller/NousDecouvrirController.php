<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NousDecouvrirController extends AbstractController
{
    #[Route('/decouvrir', name: 'app_nous_decouvrir')]
    public function index(): Response
    {
        return $this->render('nous_decouvrir/index.html.twig', [
            'controller_name' => 'NousDecouvrirController',
        ]);
    }
}
