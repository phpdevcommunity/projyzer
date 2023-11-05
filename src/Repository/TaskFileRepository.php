<?php

namespace App\Repository;

use App\Entity\TaskFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskFile>
 *
 * @method TaskFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskFile[]    findAll()
 * @method TaskFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskFile::class);
    }

//    /**
//     * @return TaskFile[] Returns an array of TaskFile objects
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

//    public function findOneBySomeField($value): ?TaskFile
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
