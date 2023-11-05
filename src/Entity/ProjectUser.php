<?php

namespace App\Entity;

use App\Repository\ProjectUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProjectUserRepository::class)]
class ProjectUser
{
    use TimestampableEntity;

    const FULL_PRIVILEGE = 'FULL_PRIVILEGE';
    const CAN_CREATE_TASK = 'CAN_CREATE_TASK';
    const CAN_EDIT_PROJECT = 'CAN_EDIT_PROJECT';
    const CAN_CLOSE_PROJECT = 'CAN_CLOSE_PROJECT';
    const CAN_DELETE_PROJECT = 'CAN_DELETE_PROJECT';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "json")]
    private array $permissions = [];

    #[ORM\ManyToOne(targetEntity: Project::class, cascade: ['persist'], inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'project_id', nullable: false)]
    private ?Project $project = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPermissions(): array
    {
        return array_unique($this->permissions);
    }

    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function canEditProject(): bool
    {
        return count(array_intersect(['FULL_PRIVILEGE', 'CAN_EDIT_PROJECT'], $this->getPermissions())) > 0;
    }

    public function canCloseProject(): bool
    {
        return count(array_intersect(['FULL_PRIVILEGE', 'CAN_CLOSE_PROJECT'], $this->getPermissions())) > 0;
    }

    public function canCreateTask(): bool
    {
        return count(array_intersect(['FULL_PRIVILEGE', 'CAN_CREATE_TASK'], $this->getPermissions())) > 0;
    }
}
