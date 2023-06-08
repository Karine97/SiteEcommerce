<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\SlugType;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

  
    public function configureFields(string $pageName): iterable // On décommente la fonction configureFields pour indiquer à
    // easyadmin quelles sont les inputs que je veux afficher et en quels formats. On va aller chercher les class de easyadmin en spécifiant le type 

    {
      return [
        TextField::new('titre'),
        SlugField::new('slug')->setTargetFieldName('titre'),
        ImageField::new('illustration')
              ->setBasePath('uploads/')
              ->setUploadDir('public/uploads')
              ->setUploadedFileNamePattern('[randomhash].[extension]')
              ->setRequired(false),
        TextField::new('sous_titre'),
        TextareaField::new('description'),
        BooleanField::new('isBest', 'Les meilleurs'),
        MoneyField::new('prix')->setCurrency('EUR'),
        AssociationField::new('categorie')


      ];
    }
    
}
