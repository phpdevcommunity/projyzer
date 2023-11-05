<?php

namespace App\Repository;

use App\Entity\OrganizationUnit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrganizationUnit>
 *
 * @method OrganizationUnit|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrganizationUnit|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrganizationUnit[]    findAll()
 * @method OrganizationUnit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationUnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationUnit::class);
    }

//    /**
//     * @return OrganizationUnit[] Returns an array of OrganizationUnit objects
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

//    public function findOneBySomeField($value): ?OrganizationUnit
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
