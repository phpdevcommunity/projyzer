<?php

namespace App\Repository;

use App\Entity\ProjectCategoryReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectCategoryReference>
 *
 * @method ProjectCategoryReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectCategoryReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectCategoryReference[]    findAll()
 * @method ProjectCategoryReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectCategoryReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectCategoryReference::class);
    }

//    /**
//     * @return ProjectCategoryReference[] Returns an array of ProjectCategoryReference objects
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

//    public function findOneBySomeField($value): ?ProjectCategoryReference
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
