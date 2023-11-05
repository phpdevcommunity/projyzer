<?php

namespace App\Repository;

use App\Entity\ProjectCategoryReferenceStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectCategoryReferenceStatus>
 *
 * @method ProjectCategoryReferenceStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectCategoryReferenceStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectCategoryReferenceStatus[]    findAll()
 * @method ProjectCategoryReferenceStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectCategoryReferenceStatusRepositoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectCategoryReferenceStatus::class);
    }

//    /**
//     * @return ProjectStatus[] Returns an array of ProjectStatus objects
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

//    public function findOneBySomeField($value): ?ProjectStatus
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
