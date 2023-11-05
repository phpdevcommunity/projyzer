<?php

namespace App\Repository;

use App\Entity\TaskStatusReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskStatusReference>
 *
 * @method TaskStatusReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskStatusReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskStatusReference[]    findAll()
 * @method TaskStatusReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskStatusReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskStatusReference::class);
    }

//    /**
//     * @return TaskStatusReference[] Returns an array of TaskStatusReference objects
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

//    public function findOneBySomeField($value): ?TaskStatusReference
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
