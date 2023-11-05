<?php

namespace App\EventListener;

use App\Entity\Organization;
use App\Entity\OrganizationUnit;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: User::class)]
class UserListener
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function postPersist(User $user, PostPersistEventArgs $event): void
    {
        $em = $event->getObjectManager();
        if ($user->isSendActivationEmail()) {
            $this->userService->sendEmailRegistration($user);
        }
    }
}