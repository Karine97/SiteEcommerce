<?php

namespace App\Form; // namespace précise l'endroit ou l'on se trouve, ici dans App\Form


use App\Service\Search;
use App\Entity\Categorie;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchType extends AbstractType // On lui donne le nom de la class SearchType qui va extends de AbstractType
                                    // de symfony Form AbstractType. En suite on va aller créer une fonction qui va nous
                                    // permettre de configurer des options (Comme dans l'exemple du fichier EnregistrementType)
{
// Création du formulaire 
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('string', TextType::class,[ // On va lui mettre les propriétés de notre formulaire
            'label' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Votre recherche...',
                'class' => 'form-control-sm'
            ]
        ])
        ->add('categories', EntityType::class, [
            'label' => false,
            'required' => false,
            'class' => Categorie::class,
            'multiple' => true,
            'expanded' => true
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Filtrer',
            'attr' => [
                'class' => 'btn-block btn-info'
            ]
        ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,// On appelle la Class Search et spécifier en deuxième option au formulaire la méthode GET pour que les données du formulaire
            'method' => 'GET',            // transit par l'url et pour que les utilisateurs puissent transmettre les urls en faisant copier/coller à d'autres utilisateurs
            'crsf_protection' => false,   // Et désactiver la crsf_protection de symfony car dans ce cas précis on a pas besoin de crypter des informations
        ]);
    }
    public function getBlockPrefix() //En utilisant la fonction getBlockPrefix nous retourne un tableau avec les valeurs dedans
                                    // Et ce tableau sera préfixé du nom de la classe à savoir Search. Et comme on ne souhaite pas 
                                    // le voir apparaître dans l'url on va lui dire de ne rien retourner. Puis aller créer le formulaire
                                    // à l'exemple de registerType
    {
        return '';
    }
}

 