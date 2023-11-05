<?php

namespace App\Entity;

use App\Repository\ProjectCategoryReferenceStatusRepositoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProjectCategoryReferenceStatusRepositoryRepository::class)]
class ProjectCategoryReferenceStatus
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $isInitial = false;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $closesTask = false;

    #[ORM\Column(name: '`order`', type: 'integer', options: ['default' => 0])]
    private int $order = 0;

    #[ORM\ManyToOne(targetEntity: ProjectCategoryReference::class, cascade: ['persist'], inversedBy: "statuses")]
    #[ORM\JoinColumn(name:'project_category_reference_id', nullable: true)]
    private ?ProjectCategoryReference $projectCategoryReference = null;

    #[ORM\ManyToOne(targetEntity: TaskStatusReference::class, cascade: ['persist'], fetch: 'EAGER')]
    #[ORM\JoinColumn(name:'task_status_reference_id', nullable: false)]
    private ?TaskStatusReference $taskStatusReference = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsInitial(): bool
    {
        return $this->isInitial;
    }

    public function setIsInitial(bool $isInitial): self
    {
        $this->isInitial = $isInitial;
        return $this;
    }

    public function isClosesTask(): bool
    {
        return $this->closesTask;
    }

    public function setClosesTask(bool $closesTask): self
    {
        $this->closesTask = $closesTask;
        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getProjectCategoryReference(): ?ProjectCategoryReference
    {
        return $this->projectCategoryReference;
    }

    public function setProjectCategoryReference(?ProjectCategoryReference $projectCategoryReference): self
    {
        $this->projectCategoryReference = $projectCategoryReference;
        return $this;
    }

    public function getTaskStatusReference(): ?TaskStatusReference
    {
        return $this->taskStatusReference;
    }

    public function setTaskStatusReference(?TaskStatusReference $taskStatusReference): self
    {
        $this->taskStatusReference = $taskStatusReference;
        return $this;
    }
}
