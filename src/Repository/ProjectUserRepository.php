<?php

namespace App\Repository;

use App\Entity\ProjectUser;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectUser>
 *
 * @method ProjectUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectUser[]    findAll()
 * @method ProjectUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectUser::class);
    }

    /**
     * @return ProjectUser[] Returns an array of ProjectUser objects
     */
    public function findAllByUser(User $user, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('project')
            ->addSelect('user')
            ->addSelect('organizationUnit')
            ->addSelect('projectCategoryReference')
            ->innerJoin('p.project', 'project')
            ->innerJoin('p.user', 'user')
            ->leftJoin('project.organizationUnit', 'organizationUnit')
            ->leftJoin('project.projectCategoryReference', 'projectCategoryReference')
            ->where('user = :user')
            ->setParameter('user', $user)
            ->orderBy('project.id', 'DESC');
        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()
            ->getResult();
    }

    public function countByUser(User $user): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->innerJoin('p.project', 'project')
            ->innerJoin('p.user', 'user')
            ->leftJoin('project.organizationUnit', 'organizationUnit')
            ->leftJoin('project.projectCategoryReference', 'projectCategoryReference')
            ->where('user = :user')
            ->setParameter('user', $user);
        return $qb->getQuery()->getSingleScalarResult();
    }

//    public function findOneBySomeField($value): ?ProjectUser
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
