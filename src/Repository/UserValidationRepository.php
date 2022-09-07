<?php

namespace Aolr\UserBundle\Repository;

use Aolr\UserBundle\Entity\UserValidation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserValidation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserValidation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserValidation[]    findAll()
 * @method UserValidation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserValidationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserValidation::class);
    }

    // /**
    //  * @return UserValidation[] Returns an array of UserValidation objects
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
    public function findOneBySomeField($value): ?UserValidation
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
