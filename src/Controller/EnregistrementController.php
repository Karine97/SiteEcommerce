<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EnregistrementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EnregistrementController extends AbstractController
            // la class EnregistrementController renvoie la vue de la page inscription
{
    private $entityManager; // c'est une variable de doctrine

    public function __construct(EntityManagerInterface $entityManager) { // quand on construit la class EnregistrementController
            // j'ai besoin que tu extencie EntityManagerInterface
        $this->entityManager = $entityManager; // mettre l'entityManager que l'on vient d'extencier

    }
    
    #[Route('/inscription', name: 'app_enregistrement')] 
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response 
            // 1/ La methode Request est extenciée avec une variable $request pour utiliser la fonction index.
            // 2/ besoin d'injecter la methode UserPasswordHasherInterface et on va lui inecter une variable $passwordHasher
    {
            // J'ai besoin d'extencier ma class User en l'injectant dans 
            // la variable user($user), en quelque sorte l'ouvrir. Ces étapes vont aller compléter 
            // notre formulaire enregistrement avec ls champs nom, prenom etc... 
        $user = new User(); // penser à importer la class User

            //besoin d'extencier mon formulaire, en l'injectant dans la variable form
            //Pour cela on va utiliser la variable $this avec la methode createForm.
            //Dans la methode createForm, il y a deux paramètre à renseigner : 
            // la class de mon formulaire "EnregistrementType::class" (dans le dossier Form) 
            // Et en deuxième paramètre la variable de l'objet user que l'on vient d'extencier
        $form = $this->createForm(EnregistrementType::class, $user);

            // 1. Dés que le formulaire est soumis
            // 2. Je veux que tu traites l'information
            // 3. Regarde si tout va bien (formulaire valide ?)
            // 4. On est bon, enregistre en base
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { // par rapport aux contraintes que l'on a mis dans le form enregisrementType
            // Rappeler l'objet User et que tu injectes dans l'objet User toutes données que tu recupère dans le formulaire 
            $user =$form->getData();
            //dd($user); // permet de voir ce qu'il y a dans la variable user

            // Maintenant je veux stocker le mot de passe de l'utilisateur dans la variable $password de l'entité User
            $password = $passwordHasher->hashPassword($user,$user->getPassword()); // et que le MDP soit hacher, on rajoute alors 
            // $passwordHasher->hashPassword avec un deuxième paramètre $user.
            //dd($password);

            // Ensuite, je veux que le MDP crypté soit réinjecté dans l'objet user en utilisant le setPassword
            $user->setPassword($password);


            // Pour enregistrer les informations dans la base de données il faut appeler doctrine
            // Et aller chercher le getManager et fige la data ($user) avec 'persist' puis excute la persistance 
            // Tu prends la data(l'objet) que tu as figé et tu l'enregistre en base de données
            $this->entityManager->persist($user); // persist prépare la donnée et la fige pour la création d'une entity pas nécessaire pour une maj de donnée
            $this->entityManager->flush();// La méthode flush permet de s'assurer que toutes les modifications sont correctement enregistrées
            // dans la base de données.
        }

            // Puis passer la variable form ($form) au template twig. 
            // Pour cela on renseigne en deuxième paramètre de la méthode render
            // un tableau avec en clé ce que l'on veut écrite et en association la variable form
            // $form en lui demandant de créer la view
        return $this->render('enregistrement/index.html.twig', [
            'monform' => $form->createView() 
            
        ]);
    }
}
