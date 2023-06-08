<?php

namespace App\Repository;


use App\Entity\Produit;
use App\Service\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function save(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }   
    }

    public function remove(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Requête qui me permet de récupérer les produits en fonction de la rechercher de l'utilisateur
     * @return Produit[]
     */
    public function findWithSearch(Search $search) // Nouvelle fonction findWithSearch qui prend en paramètre la class Search que l'on stock
    // dans une variable $search. Qui permet de construire notre recherche
    { // Besoin de créer variable $query et à l'intérieur de cette query utiliser plusieurs méthodes
        $query = $this
            ->createQueryBuilder('p') // permet de commencer une query avec la table produits ('p') pour faire le Mapping
            ->select('c', 'p') // permet de selectionner la categorie(c) et le produit(p)
            ->join('p.categorie', 'c'); // et une jointure entre les categories et la table categorie

        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }

        if (!empty($search->string)) {
            $query = $query
                ->andWhere('p.titre LIKE :string')
                ->setParameter('string', "%{$search->string}%");// Pour lui indiquer de faire une recherche partielle
                                                                // sur search string on doit le mettre dans des cotes avec des %
                                                                //  "%{$search->string}%"
        }
    
        
        return $query->getQuery()->getResult();// Je veux que tu me retournes la query, que tu l'exécute et la créer
                                                // et que tu me retournes les résultats
    }


//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
