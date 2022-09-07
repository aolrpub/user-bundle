<?php

namespace Aolr\UserBundle\Repository;

use Aolr\UserBundle\Entity\UserJobType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserJobType|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserJobType|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserJobType[]    findAll()
 * @method UserJobType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserJobTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserJobType::class);
    }

    // /**
    //  * @return UserJobType[] Returns an array of UserJobType objects
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
    public function findOneBySomeField($value): ?UserJobType
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
