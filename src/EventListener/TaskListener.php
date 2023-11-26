<?php

namespace App\EventListener;

use App\Entity\Organization;
use App\Entity\OrganizationUnit;
use App\Entity\Task;
use App\Entity\User;
use App\Service\TaskService;
use App\Service\UserService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Task::class)]
class TaskListener
{
    public function __construct(private readonly Security $security, private readonly TaskService $taskService)
    {
    }

    public function postUpdate(Task $task, PostUpdateEventArgs $event): void
    {
        $user = $this->security->getUser();
        $task->setUpdatedBy($user);
        $this->taskService->notify($task);
    }
}