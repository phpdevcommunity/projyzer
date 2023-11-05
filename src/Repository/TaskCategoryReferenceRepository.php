<?php

namespace App\Repository;

use App\Entity\TaskCategoryReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskCategoryReference>
 *
 * @method TaskCategoryReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskCategoryReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskCategoryReference[]    findAll()
 * @method TaskCategoryReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskCategoryReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskCategoryReference::class);
    }

//    /**
//     * @return TaskCategoryReference[] Returns an array of TaskCategoryReference objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TaskCategoryReference
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
