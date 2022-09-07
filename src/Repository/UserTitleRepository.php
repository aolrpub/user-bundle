<?php

namespace Aolr\UserBundle\Repository;

use Aolr\UserBundle\Entity\UserTitle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserTitle|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTitle|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTitle[]    findAll()
 * @method UserTitle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTitleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTitle::class);
    }

    // /**
    //  * @return UserTitle[] Returns an array of UserTitle objects
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
    public function findOneBySomeField($value): ?UserTitle
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
