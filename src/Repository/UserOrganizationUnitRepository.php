<?php

namespace App\Repository;

use App\Entity\UserOrganizationUnit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserOrganizationUnit>
 *
 * @method UserOrganizationUnit|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserOrganizationUnit|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserOrganizationUnit[]    findAll()
 * @method UserOrganizationUnit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserOrganizationUnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserOrganizationUnit::class);
    }

//    /**
//     * @return UserOrganizationUnit[] Returns an array of UserOrganizationUnit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserOrganizationUnit
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
