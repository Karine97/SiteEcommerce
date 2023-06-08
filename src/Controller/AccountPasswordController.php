<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{
    private $entityManager; // On extencie la variable $entityManager (c'est une variable de doctrine) en faisant une injection de dépendance.
    public function __construct(EntityManagerInterface $entityManager) { // On initialise le constructeur en extenciant EntityManagerInterface et en stockant EntityManager
        $this->entityManager = $entityManager; // mettre l'entityManager que l'on vient d'extencier

    }

    #[Route('/account/modifier-mdp', name: 'app_account_password')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response // on aura besoin de passer à la fonction la request en injection de dépendance
    // avec le symfony component foundation et et met la request dans une variable qui s'appelle $request
    {
        // On peut déclarer une variable $notification pour informer l'utilisateur que son mdp a été maj.
        $notification = null;

        // A cet étape, j'ai besoin d'appeler mon formulaire createForm() et la class ChangePasswordType::class
        // Et en deuxième paramètre l'objet user de la classe à laquelle est liée le formulaire et c'est donc User. 
        // On va devoir appeler l'objet user en utilisant un $this->getUser  et l'injecter dans la variable ma 
        // premiere variable ($user) et la passer à ma deuxième variable de mon formulaire ($user)
        $user=$this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        // On va traiter le formulaire avec handlerrequest
        $form->handleRequest($request);

        // On valide si le formulaire a été soumis et validé
        if ($form->isSubmitted() && $form->isValid()) {
        // On va avoir besoin d'une méthode qui permet de comprarer le nouveau mot de passe saisi par l'utilisateur et le mot de passe
        // que l'on a en BD crypté. Je vais avoir besoin de la methode UserPasswordHasherInterface et que l'on va stocker dans une variable $passwordHasher.
        // On récupère $old_pword, je lui rapelle $form, je lui dit d'aller get old_password et getData pour récupérer la donnée
            $old_pword = $form->get('old_password')->getData();
            // dd($old_pword); pour vérifier l'ancien mdp

         // isPasswordValid prend deux paramètres 'user et l'ancien MDP actuel mais non cripté'. Si ça renvoie 'true' cela veut dire que les mots de passe sont identiques
            if ($passwordHasher->isPasswordValid($user, $old_pword)) {
             //  die('CA MARCHE'); // Pour vérifier que le nouveau mdp est valide dans l'entity user avec le MDP hascher en BD et l'ancien mdp pour vérifier si s'était bon
            // va hacher l'ancien mdp pour comparer les deux mdp (nouveau et ancien)
            // Maintenant on va lui demander d'aller chercher le nouveau mdp $new_pword,je lui rapelle $form, je lui dit d'aller get new_password et getData pour récupérer la donnée
            $new_pword = $form->get('new_password')->getData();
            //dd($new_pword); Un débug de new_pword pour voir si j'ai le nouveau mdp
            // Alors demander que le mdp soit hacher
            $password = $passwordHasher->hashPassword($user, $new_pword);

            $user->setPassword($password);
            // et de dire à mon entityManager (doctrine) d'enregistrer en BD 
            $this->entityManager->flush();// La méthode flush permet de s'assurer que toutes les modifications sont correctement enregistrées
            // dans la base de données.

            // Après que la maj a été effectuée, un message qui informe l'utilisateur que son mdp a bien été mis à jour
            $notification = "Votre mot de passe a bien été mis à jour.";

            } else {
                $notification = "Votre mot de passe est erroné.";
            }
        }

        return $this->render('account/password.html.twig', [ // on passe un tableau à la vue twig le form avec un createView
            'form' => $form->createView(),
            // Passer la variable à twig pour qu'elle l'affiche si elle est différente de null
            'notification' => $notification
            
        ]);

    }

}
