<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //->add('roles')On l'enlève la proriété (roles) car on ne souhaite pas que l'utilisateur choisisse son rôle.
        $builder
            ->add('email', EmailType::class, [ // on peut rajouter des contraintes pour que l'utilisateur ne puisse pas modifier les informations du formulaire en utilisant 'disabled'
                'disabled' => true,
                'label' => 'Adresse email'
            ])
            ->add('prenom', TextType::class, [
                'disabled' => true,
                'label' => 'Prénom'
            ])
            ->add('nom', TextType::class, [
                'disabled' => true,
                'label' => 'Nom'
            ])
            ->add('old_password', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false, // idem que pour new_password
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre mot de passe actuel'
                ]
            ])
            ->add('new_password', RepeatedType::class, [ // RepeatedType permet de répéter le mdp et 
                // pour une même propriété de généré 2 champs différents qui doivent avoir le même contenu (mdp) pour cela il faut rajouter :
                'type' => PasswordType::class,
                // Je rajoute manuellement une nouvelle clef 'mapped' que l'on met 'false' pour lui dire de ne pas lier ce champs avec mon entity User   
                // car le navigateur ne pourrais pas trouver le chemin dans entity User ou se trouve la propriété new_password
                // car cette propriété n'existe pas
                'mapped' => false,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identique', 
                'label' => 'Mon nouveau mot de passe',
                'required' => true, // Pour dire que ce champs est obligatoire
                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                'attr' => [
                    'placeholder' => 'Merci de saisir votre nouveau mot de passe.'
                ]
            ], 
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                        'placeholder' => 'Merci de confirmer votre nouveau mot de passe.'
                ]
            ]
        ])
            ->add('submit', SubmitType::class, [
            'label' => 'Mettre à jour'
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
