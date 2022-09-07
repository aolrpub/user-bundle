<?php

namespace Aolr\UserBundle\Repository;

use Aolr\UserBundle\Entity\UserWorkplace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserWorkplace|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserWorkplace|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserWorkplace[]    findAll()
 * @method UserWorkplace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserWorkplaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWorkplace::class);
    }

    // /**
    //  * @return UserWorkplace[] Returns an array of UserWorkplace objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserWorkplace
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
