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

class EnregistrementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                'attr' => [
                    'placeholder' => 'Merci de saisir votre prénom'
                ]
            ])

            ->add('nom', TextType::class,[
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Merci de saisir votre nom'
                ]
            ])

            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'attr' => [
                    'placeholder' => 'Merci de saisir votre adresse email'
                ]
            ])
            ->add('password', RepeatedType::class, [ // RepeatedType permet de dire à symfony que j'ai besoin
                // pour une même propriété de généré 2 champs différents qui doivent le même contenu pour cela il faut rajouter :
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identique', 
                'label' => 'Votre password',
                'required' => true, // Pour dire que ce champs est obligatoire
                'first_options' => ['label' => 'Mot de passe'], 
                'second_options' => ['label' => 'Confirmez votre mot de passe']
            ])
           
            ->add('submit', SubmitType::class, [
                'label' => 'Sinscrire'
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
