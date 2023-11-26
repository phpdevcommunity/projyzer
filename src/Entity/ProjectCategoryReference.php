<?php

namespace App\Entity;

use App\Repository\ProjectCategoryReferenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProjectCategoryReferenceRepository::class)]
class ProjectCategoryReference
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: "projectCategoryReference", targetEntity: ProjectCategoryReferenceStatus::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private iterable $projectCategoryReferenceStatuses;

    #[ORM\ManyToOne(targetEntity: OrganizationUnit::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name:'organization_unit_id', nullable: false)]
    private ?OrganizationUnit $organizationUnit = null;

    public function __construct()
    {
        $this->projectCategoryReferenceStatuses = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('#%s - %s', $this->getId(), $this->getLabel());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStatuses(): iterable
    {
        return $this->getProjectCategoryReferenceStatuses()->map(function (ProjectCategoryReferenceStatus $status) {
            return $status->getTaskStatusReference();
        })->toArray();
    }

    public function addStatus(TaskStatusReference $taskStatusReference) : self
    {
        $this->addProjectCategoryReferenceStatus(
            (new ProjectCategoryReferenceStatus())->setTaskStatusReference($taskStatusReference)
        );
        return $this;
    }

    public function removeStatus(TaskStatusReference $taskStatusReference) : self
    {
        $statuses = $this->getProjectCategoryReferenceStatuses()->filter(function (ProjectCategoryReferenceStatus $projectCategoryReferenceStatus) use ($taskStatusReference) {
            return $projectCategoryReferenceStatus->getTaskStatusReference() === $taskStatusReference;
        });

        foreach ($statuses as $status) {
            $this->getProjectCategoryReferenceStatuses()->removeElement($status);
        }
        return $this;
    }

    public function getProjectCategoryReferenceStatuses(): Collection
    {
        return $this->projectCategoryReferenceStatuses;
    }

    public function addProjectCategoryReferenceStatus(ProjectCategoryReferenceStatus $status): self
    {
        $status->setProjectCategoryReference($this);
        $statuses = $this->getProjectCategoryReferenceStatuses()->filter(function (ProjectCategoryReferenceStatus $projectCategoryReferenceStatus) use ($status) {
            return $projectCategoryReferenceStatus->getTaskStatusReference() === $status?->getTaskStatusReference();
        });
        if ($statuses->isEmpty()) {
            $this->getProjectCategoryReferenceStatuses()->add($status);
        }
        return $this;
    }

    public function removeProjectCategoryReferenceStatus(ProjectCategoryReferenceStatus $status) : self
    {
        if ($this->projectCategoryReferenceStatuses->contains($status)) {
            $this->projectCategoryReferenceStatuses->removeElement($status);
        }
        return $this;
    }

    public function getOrganizationUnit(): ?OrganizationUnit
    {
        return $this->organizationUnit;
    }

    public function setOrganizationUnit(OrganizationUnit $organizationUnit): self
    {
        $this->organizationUnit = $organizationUnit;
        return $this;
    }
}
