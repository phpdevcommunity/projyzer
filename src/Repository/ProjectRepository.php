<?php

namespace App\Repository;

use App\Entity\OrganizationUnit;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }


    /**
     * @param OrganizationUnit $organizationUnit
     * @return array<Project>
     */
    public function findByOrganizationUnit(OrganizationUnit $organizationUnit): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('organizationUnit')
            ->addSelect('projectCategoryReference')
            ->leftJoin('p.organizationUnit', 'organizationUnit')
            ->leftJoin('p.projectCategoryReference', 'projectCategoryReference')
            ->where('p.organizationUnit = :organizationUnit')
            ->setParameter('organizationUnit', $organizationUnit)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Project
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
