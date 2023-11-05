<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Entity\OrganizationUnit;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return Task[] Returns an array of Task objects
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('assignedTo')
            ->addSelect('taskCategoryReference')
            ->addSelect('project')
            ->addSelect('organizationUnit')
            ->addSelect('lastStatus')
            ->leftJoin('t.lastStatus', 'lastStatus')
            ->leftJoin('t.project', 'project')
            ->leftJoin('t.taskCategoryReference', 'taskCategoryReference')
            ->leftJoin('t.assignedTo', 'assignedTo')
            ->leftJoin('t.createdBy', 'createdBy')
            ->leftJoin('project.organizationUnit', 'organizationUnit')
            ->andWhere('t.assignedTo = :user OR t.createdBy = :user')
            ->setParameter('user', $user->getId())
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByUser(User $user): int
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->leftJoin('t.lastStatus', 'lastStatus')
            ->leftJoin('t.project', 'project')
            ->leftJoin('t.taskCategoryReference', 'taskCategoryReference')
            ->leftJoin('t.assignedTo', 'assignedTo')
            ->leftJoin('t.createdBy', 'createdBy')
            ->leftJoin('project.organizationUnit', 'organizationUnit')
            ->andWhere('t.assignedTo = :user OR t.createdBy = :user')
            ->setParameter('user', $user->getId())
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByOrganization(OrganizationUnit $organizationUnit): int
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->leftJoin('t.lastStatus', 'lastStatus')
            ->leftJoin('t.project', 'project')
            ->leftJoin('t.taskCategoryReference', 'taskCategoryReference')
            ->leftJoin('t.assignedTo', 'assignedTo')
            ->leftJoin('t.createdBy', 'createdBy')
            ->leftJoin('project.organizationUnit', 'organizationUnit')
            ->andWhere('organizationUnit = :organizationUnit')
            ->setParameter('organizationUnit', $organizationUnit)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Task[] Returns an array of Task objects
     */
    public function findByProject(Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('assignedTo')
            ->addSelect('taskCategoryReference')
            ->addSelect('project')
            ->addSelect('organizationUnit')
            ->addSelect('lastStatus')
            ->addSelect('createdBy')
            ->addSelect('assignedTo')
            ->leftJoin('t.lastStatus', 'lastStatus')
            ->leftJoin('t.project', 'project')
            ->leftJoin('t.taskCategoryReference', 'taskCategoryReference')
            ->leftJoin('t.assignedTo', 'assignedTo')
            ->leftJoin('t.createdBy', 'createdBy')
            ->leftJoin('project.organizationUnit', 'organizationUnit')
            ->andWhere('t.project = :project')
            ->setParameter('project', $project->getId())
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
