<?php

namespace App\Repository;

use App\Entity\FoodPointing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FoodPointing|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoodPointing|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoodPointing[]    findAll()
 * @method FoodPointing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodPointingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodPointing::class);
    }

    // /**
    //  * @return FoodPointing[] Returns an array of FoodPointing objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FoodPointing
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
