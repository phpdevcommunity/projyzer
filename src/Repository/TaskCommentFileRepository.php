<?php

namespace App\Repository;

use App\Entity\TaskCommentFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskCommentFile>
 *
 * @method TaskCommentFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskCommentFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskCommentFile[]    findAll()
 * @method TaskCommentFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskCommentFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskCommentFile::class);
    }

//    /**
//     * @return TaskCommentFile[] Returns an array of TaskCommentFile objects
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

//    public function findOneBySomeField($value): ?TaskCommentFile
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
