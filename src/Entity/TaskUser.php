<?php

namespace App\Entity;

use App\Repository\TaskUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TaskUserRepository::class)]
class TaskUser
{
    const FULL_PRIVILEGE = 'FULL_PRIVILEGE';
    const CAN_EDIT_TASK = 'CAN_EDIT_TASK';
    const CAN_DELETE_TASK = 'CAN_DELETE_TASK';
    const CAN_COMMENT = 'CAN_COMMENT';

    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "json")]
    private array $permissions = [];

    #[ORM\ManyToOne(targetEntity: Task::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'users')]
    #[ORM\JoinColumn(name:'task_id', nullable: false)]
    private ?Task $task = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name:'user_id', nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;
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

    public function canComment(): bool
    {
        return count(array_intersect(['FULL_PRIVILEGE', 'CAN_COMMENT'], $this->getPermissions())) > 0;
    }

    public function canEdit(): bool
    {
        return count(array_intersect(['FULL_PRIVILEGE', 'CAN_EDIT_TASK'], $this->getPermissions())) > 0;
    }
}
