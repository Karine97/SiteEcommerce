<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'attr' => [
                    'palceholder' => 'Veuillez saisir votre mot de passe actuel'
                ]
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
