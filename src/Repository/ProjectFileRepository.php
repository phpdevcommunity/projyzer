<?php

namespace App\Repository;

use App\Entity\ProjectFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectFile>
 *
 * @method ProjectFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectFile[]    findAll()
 * @method ProjectFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectFile::class);
    }

//    /**
//     * @return ProjectFile[] Returns an array of ProjectFile objects
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

//    public function findOneBySomeField($value): ?ProjectFile
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
