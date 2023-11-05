<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    #[Gedmo\Slug(fields: ['name'], updatable: true, unique: true)]
    protected ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "boolean", options: ['default' => 0])]
    private bool $active = false;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_id', nullable: true, onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: OrganizationUnit::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'organization_unit_id', nullable: false)]
    private ?OrganizationUnit $organizationUnit = null;

    #[ORM\OneToMany(mappedBy: "project", targetEntity: ProjectUser::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private iterable $users;

    #[ORM\OneToMany(mappedBy: "project", targetEntity: ProjectFile::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private iterable $files;

    #[ORM\ManyToOne(targetEntity: ProjectCategoryReference::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'project_category_reference_id', nullable: true)]
    private ?ProjectCategoryReference $projectCategoryReference = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
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

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): Project
    {
        $this->active = $active;
        return $this;
    }


    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;
        if ($createdBy instanceof User) {
            $this->addUser($createdBy, [ProjectUser::FULL_PRIVILEGE]);
        }
        return $this;
    }

    public function getOrganizationUnit(): ?OrganizationUnit
    {
        return $this->organizationUnit;
    }

    public function setOrganizationUnit(?OrganizationUnit $organizationUnit): self
    {
        $this->organizationUnit = $organizationUnit;
        return $this;
    }

    /**
     * @return Collection<ProjectUser>
     */
    public function getProjectUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection<ProjectUser>
     */
    public function getProjectUsersWithoutOwner(): Collection
    {
        return $this->users->filter(function (ProjectUser $projectUser) {
            return $projectUser->getUser() !== $this->getCreatedBy();
        });
    }

    public function addProjectUsersWithoutOwner(ProjectUser $projectUser): self
    {
        return $this->addProjectUser($projectUser);
    }

    public function removeProjectUsersWithoutOwner(ProjectUser $projectUser): self
    {
        return $this->removeProjectUser($projectUser);
    }

    public function addProjectUser(ProjectUser $projectUser): self
    {
        $projectUser->setProject($this);
        $users = $this->getUsers()->filter(function (User $forUser) use ($projectUser) {
            return $forUser === $projectUser->getUser();
        });
        if ($users->isEmpty()) {
            $this->users->add($projectUser);
        }
        return $this;
    }

    public function removeProjectUser(ProjectUser $projectUser): self
    {
        return $this->removeUser($projectUser->getUser());
    }

    public function getUsers(): Collection
    {
        return $this->users->map(function (ProjectUser $projectUser) {
            return $projectUser->getUser();
        });
    }

    public function addUser(User $user, array $permissions = [ProjectUser::CAN_CREATE_TASK]): self
    {
        $projectUser = (new ProjectUser())
            ->setPermissions($permissions)
            ->setUser($user);

        return $this->addProjectUser($projectUser);
    }

    public function removeUser(User $user): self
    {
        foreach ($this->getProjectUsers() as $projectUser) {
            if ($projectUser->getUser() === $user) {
                $this->getProjectUsers()->removeElement($projectUser);
            }
        }
        return $this;
    }

    public function getFiles(): iterable
    {
        return $this->files;
    }

    public function addFile(ProjectFile $file): self
    {
        $file->setProject($this);
        if (!$this->files->contains($file)) {
            $this->files->add($file);
        }
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

    public function canAccess(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin() && $user->getOrganizationUnit() === $this->getOrganizationUnit()) {
            return true;
        }
        if ($this->getUsers()->contains($user)) {
            return true;
        }

        return false;
    }

    public function canEditProject(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin() && $user->getOrganizationUnit() === $this->getOrganizationUnit()) {
            return true;
        }

        foreach ($this->getProjectUsers() as $projectUser) {
            if ($projectUser->getUser() === $user && $projectUser->canEditProject()) {
                return true;
            }
        }
        return false;
    }

    public function canCloseProject(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin() && $user->getOrganizationUnit() === $this->getOrganizationUnit()) {
            return true;
        }

        foreach ($this->getProjectUsers() as $projectUser) {
            if ($projectUser->getUser() === $user && $projectUser->canCloseProject()) {
                return true;
            }
        }
        return false;
    }

    public function canCreateTask(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin() && $user->getOrganizationUnit() === $this->getOrganizationUnit()) {
            return true;
        }

        foreach ($this->getProjectUsers() as $projectUser) {
            if ($projectUser->getUser() === $user && $projectUser->canCreateTask()) {
                return true;
            }
        }
        return false;
    }
}
