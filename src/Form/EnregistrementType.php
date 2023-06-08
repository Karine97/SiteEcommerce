<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EnregistrementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    // le rôle de cette fonction "buildForm" est de créer un formulaire et on lui demande d'ajouter des propriétés. 
    //->add('roles')On l'enlève la proriété (roles) car on ne souhaite pas que l'utilisateur choisisse son rôle.

    // 1/Ensuite pour améliorer le visuel du formulaire d'enregistrement, en allant dans la doc symfony
    // Dans component j'ai pris dans form (learn more) j'ai récupéré un code twig qui intègre du bootstrap =
    // form_themes: ['bootstrap_4_layout.html.twig'] Et je l'ai copié/collé dans config/packages/twig.yaml

    // 2/ Pour rajouter des nouveaux champs, il faut d'abord utiliser
    // l'entity User (table) et faire un symfony console make:entity
    // Sur le class User
    // Comme l'entité existe déjà, il suffit de rajouter les proprétés (champs) que l'on souhaite ajouter 
    // ('prenom', 'nom' et confirmation du MDP)
    // Puis faire la migration dans la BDD

    // 3/ Rajouter les champs manuellement dans notre formulaire EnregistrementType :
    // ->add('prenom', TextType::class,['label' => 'Votre prénom','attr' => ['placeholder' => 'Merci de saisir votre prénom'

    // Et ne pas oublier d'importer les class TextType,EmailType,SubmitType etc..;


    {
        $builder // reprend les propriétés de l'entité User
            ->add('prenom', TextType::class,[
                'label' => 'Votre prénom',
                'constraints' => new Length([// On peut rajouter des contraintes à notre formulaire, en mettant une longueur de chaîne de caractères 
                    'min' => 2, // comprise entre une valeur minimale et une valeur maximale (par exemple le prénom, nom, email)
                    'max' => 30
                ]), 
        ])

            ->add('nom', TextType::class,[
                'label' => 'Votre nom',
                'constraints' => new Length([
                    'min' => 2, 
                    'max' => 30
                ]),
        ])

            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'constraints' => new Length([
                    'min' => 2, 
                    'max' => 50
                ]),
        ])
            ->add('password', RepeatedType::class, [ // RepeatedType permet de répéter le mdp et 
                // pour une même propriété de généré 2 champs différents qui doivent avoir le même contenu (mdp) pour cela il faut rajouter :
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identique', 
                'label' => 'Votre password',
                'required' => true, // Pour dire que ce champs est obligatoire
                'first_options' => [
                    'label' => 'Mot de passe',
                'attr' => [
                    'placeholder' => 'Saisissez votre mot de passe.'
                ]
            ], 
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmez votre mot de passe.'
                ]
            ]
        ])
            ->add('conditions', CheckboxType::class, [
            'label' => 'J ai lu et accepte la politique de confidentialité',
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'You should agree to our terms.'
                    
                ]),
            ],
        ])
           
            ->add('submit', SubmitType::class, [
                'label' => 'Sinscrire'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
