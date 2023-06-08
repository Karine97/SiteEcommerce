<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findSuccessOrders($user) // findSucessOrders permet d'afficher les commandes dans le compte de l'utilisateur
    {
       return $this->createQueryBuilder('o') // createQueryBuilder est une méthode à laquelle on lui passe l'alias o de orders qui ce dernier est lié à Order du Repository
       ->andwhere('o.isPaid = 1') // on va appliquer une première condition en lui disant d'aller chercher dans orders isPaid la commande quand t-elle est payée
       ->andwhere('o.utilisateur = :user')// On demande à que soit afficher uniquement les commandes liées à mon utilisateur et on rajoute un flag :user
       ->setParameter('user', $user)// ici on lui indique que le flag :user ci-dessus correspond à la variable $user que l'on va passer en variable de fonction
       ->orderBy('o.id', 'DESC') // tu affiches les commandes par id de façon descendante DESC (décroissant)
       ->getQuery()
       ->getResult();
    }

//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
