<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }
    public function configureActions(Actions $actions): Actions  // Je veux avoir la possibilité de voir ma commande et de modifier le status. 
    //A savoir que modifier, supprimer ect.. Easyadmin appel cela des Actions
    {
        return $actions// Je vais return action avec un add et dans ce add, je vais dire dans qu'elle route je souhaite ajouter mon action
        //en loccurence dans la route 'index' et l'action name sera 'detail'
            ->add('index', 'detail');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

   
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createAt', 'Passée le'), // En mettant en deuxième paramètre Passée le cela modifie l'affichage de EasyAdmin
            TextField::new('utilisateur.fullname', 'Utilisateur'),
            MoneyField::new('total', 'Total produit')->setCurrency('EUR'),
            TextField::new('transportNom', 'Transporteur'),
            MoneyField::new('transportPrix', 'Frais de port')->setCurrency('EUR'),
            BooleanField::new('isPaid', 'Payée'),
            ArrayField::new('orderDetails', 'Produits achtés')->hideOnIndex() // Pour masquer la colonne produits achetés sur le dashbord
        ];
    }
   
}
