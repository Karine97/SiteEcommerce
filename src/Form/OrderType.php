<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Transporteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dd($options); // pour voir si l'objet user est bien intégré et pour pouvoir l'exploiter
        $user = $options['user'];

        $builder
            ->add('addresses', EntityType::class, [
                'label' => false,
                'required' => true, // champs requis vrai
                'class' => Address::class, // Avec quelle class on souhaite faire le lien avec ce champs du formulaire = class Address
                'choices' => $user->getAddresses(),
                'multiple' => false, // On ne veut pas que le choix soit multiple
                'expanded' => true // Pour indiquer que l'on veut quand même des radios button
            ])
            ->add('carriers', EntityType::class, [
                'label' => 'Choisissez votre transporteur',
                'required' => true, 
                'class' => Transporteur::class,
                'multiple' => false, 
                'expanded' => true 
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider votre commande',
                'attr' => [
                    'class' => 'btn btn-success btn-block'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => array() // On lui passe user et on lui indique que c'est un tableau vide pour le moment
            // Configure your form options here
        ]);
    }
}
