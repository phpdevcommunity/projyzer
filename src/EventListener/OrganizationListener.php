<?php

namespace App\EventListener;

use App\Entity\Organization;
use App\Entity\OrganizationUnit;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Organization::class)]
class OrganizationListener
{
    public function postPersist(Organization $organization, PostPersistEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $organizationUnit = $event->getObjectManager()->getRepository(OrganizationUnit::class)->findOneBy(['identifier' => $organization->getIdentifier()]);
        if ($organizationUnit === null) {
            $organizationUnit = (new OrganizationUnit())
                ->setOrganization($organization)
                ->setName($organization->getName())
                ->setCode('0001')
                ->setIdentifier($organization->getIdentifier());
            $em->persist($organizationUnit);
            $em->flush($organizationUnit);
        }
    }
}