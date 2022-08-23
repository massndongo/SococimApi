<?php

namespace App\Repository;

use App\Entity\VisitorCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VisitorCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitorCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisitorCard[]    findAll()
 * @method VisitorCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitorCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitorCard::class);
    }

    // /**
    //  * @return VisitorCard[] Returns an array of VisitorCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VisitorCard
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
