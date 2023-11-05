<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    const PRIORITY_LOW = 'LOW';
    const PRIORITY_MEDIUM = 'MEDIUM';
    const PRIORITY_HIGH = 'HIGH';

    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 255)]
    private ?string $title = null;
    #[ORM\Column(length: 255)]
    private ?string $description = null;
    #[ORM\Column(length: 255)]
    private ?string $priority = self::PRIORITY_LOW;
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $estimatedTime = null;
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $actualTime = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTimeInterface $startDate;
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTimeInterface $dueDate;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Assert\Range(notInRangeMessage: "The percentage must be between 0 and 100.", min: 0, max: 100)]
    private int $percentageCompleted = 0;
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?User $createdBy = null;
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'updated_by_id', nullable: true)]
    private ?User $updatedBy = null;
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'assigned_to_id', nullable: true)]
    private ?User $assignedTo = null;
    #[ORM\ManyToOne(targetEntity: Project::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'project_id', nullable: false)]
    private ?Project $project = null;
    #[ORM\ManyToOne(targetEntity: TaskCategoryReference::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'task_category_reference_id', nullable: true)]
    private ?TaskCategoryReference $taskCategoryReference = null;
    #[ORM\OneToMany(mappedBy: "task", targetEntity: TaskFile::class, cascade: ['persist', 'remove'])]
    private iterable $files;

    #[ORM\OneToMany(mappedBy: "task", targetEntity: TaskComment::class, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(["id" => "ASC"])]
    private iterable $comments;

    #[ORM\OneToMany(mappedBy: "task", targetEntity: TaskStatus::class, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(["id" => "ASC"])]
    private iterable $statuses;

    #[ORM\OneToOne(targetEntity: TaskStatus::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'last_status_id', nullable: true)]
    private ?TaskStatus $lastStatus = null;

    private ?TaskStatusReference $taskStatusReference = null;

    #[ORM\OneToMany(mappedBy: "task", targetEntity: TaskUser::class, cascade: ['persist', 'remove'])]
    private iterable $users;

    #[ORM\Column(type: "boolean", options: ['default' => 1])]
    private bool $active = true;

    public function __construct()
    {
        $this->statuses = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->startDate = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getEstimatedTime(): ?string
    {
        return $this->estimatedTime;
    }

    public function setEstimatedTime(?string $estimatedTime): self
    {
        $this->estimatedTime = $estimatedTime;
        return $this;
    }

    public function getActualTime(): ?string
    {
        return $this->actualTime;
    }

    public function setActualTime(?string $actualTime): self
    {
        $this->actualTime = $actualTime;
        return $this;
    }

    public function getPercentageCompleted(): int
    {
        return $this->percentageCompleted;
    }

    public function setPercentageCompleted(int $percentageCompleted): self
    {
        $this->percentageCompleted = $percentageCompleted;
        return $this;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getDueDate(): ?DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): self
    {
        $this->assignedTo = $assignedTo;
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

    public function getTaskCategoryReference(): ?TaskCategoryReference
    {
        return $this->taskCategoryReference;
    }

    public function setTaskCategoryReference(?TaskCategoryReference $taskCategoryReference): self
    {
        $this->taskCategoryReference = $taskCategoryReference;
        return $this;
    }

    public function getFiles(): iterable
    {
        return $this->files;
    }

    public function addFile(TaskFile $file): self
    {
        $file->setTask($this);
        if (!$this->files->contains($file)) {
            $this->files->add($file);
        }
        return $this;
    }

    public function getComments(): iterable
    {
        return $this->comments;
    }

    public function addComment(TaskComment $comment): self
    {
        $comment->setTask($this);
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
        return $this;
    }

    public function getStatuses(): iterable
    {
        return $this->statuses;
    }

    public function addStatus(TaskStatus $status): self
    {
        $status->setTask($this);
        if (!$this->statuses->contains($status)) {
            $this->statuses->add($status);
            $this->lastStatus = $status;
        }
        return $this;
    }

    public function getLastStatus(): ?TaskStatus
    {
        return $this->lastStatus;
    }


    public function getTaskStatusReference(): ?TaskStatusReference
    {
        return $this->taskStatusReference;
    }

    public function setTaskStatusReference(TaskStatusReference $taskStatusReference): Task
    {
        $this->taskStatusReference = $taskStatusReference;
        if ($this->getLastStatus()?->getLabel() != $taskStatusReference->getLabel()) {
            $this->addStatus((new TaskStatus())->setLabel($taskStatusReference->getLabel()));
        }

        return $this;
    }

    /**
     * @return Collection<TaskUser>
     */
    public function getTaskUsers(): Collection
    {
        return $this->users;
    }

    public function addTaskUser(TaskUser $taskUser): self
    {
        $taskUser->setTask($this);
        $users = $this->getUsers()->filter(function (User $forUser) use ($taskUser) {
            return $forUser === $taskUser->getUser();
        });
        if ($users->isEmpty()) {
            $this->users->add($taskUser);
        }
        return $this;
    }

    public function removeTaskUser(TaskUser $taskUser): self
    {
        return $this->removeUser($taskUser->getUser());
    }

    public function getUsers(): Collection
    {
        return $this->users->map(function (TaskUser $taskUser) {
            return $taskUser->getUser();
        });
    }

    public function addUser(User $user, array $permissions = [TaskUser::CAN_COMMENT]): self
    {
        $taskUser = (new TaskUser())
            ->setPermissions($permissions)
            ->setUser($user);

        return $this->addTaskUser($taskUser);
    }

    public function removeUser(User $user): self
    {
        foreach ($this->getTaskUsers() as $taskUser) {
            if ($taskUser->getUser() === $user) {
                $this->getTaskUsers()->removeElement($taskUser);
            }
        }
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function canAccess(User $user): bool
    {
        $project = $this->getProject();
        if ($project->canAccess($user)) {
            return true;
        }

        if ($this->getUsers()->contains($user)) {
            return true;
        }
        return false;
    }

    public function canComment(User $user): bool
    {
        $project = $this->getProject();
        if ($project->canAccess($user)) {
            return true;
        }

        foreach ($this->getTaskUsers() as $taskUser) {
            if ($taskUser->getUser() === $user && $taskUser->canComment()) {
                return true;
            }
        }
        return false;
    }

    public function canEdit(User $user): bool
    {
        $project = $this->getProject();
        if ($project->canAccess($user)) {
            return true;
        }

        foreach ($this->getTaskUsers() as $taskUser) {
            if ($taskUser->getUser() === $user && $taskUser->canEdit()) {
                return true;
            }
        }
        return false;
    }
}
