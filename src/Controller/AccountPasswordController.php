<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    #[Route('/account/modifier-mdp', name: 'app_account_password')]
    public function index(): Response
    {
        // A cet étape, j'ai besoin d'appeler mon formulaire createForm() et la class ChangePasswordType::class
        // Et en deuxième paramètre l'objet user de la classe à laquelle est liée le formulaire et c'est donc User. 
        // On va devoir appeler l'objet user en utilisant un $this->getUser  et l'injecter dans la variable ma 
        // premiere variable ($user) et la passer à ma deuxième variable de mon formulaire ($user)
        $user=$this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        return $this->render('account/password.html.twig', [ // on passe un tableau à la vue twig le form avec un createView
            'form' => $form->createView()
            
        ]);
    }
}
