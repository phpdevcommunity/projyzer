<?php

namespace App\Model\Install;


use Symfony\Component\Validator\Constraints as Assert;

final class InstallationData
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $organizationName = null;
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $organizationIdentifier = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $username = null;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: "Le mot de passe doit comporter {{ limit }} caractères minimum.",
        maxMessage: "Le mot de passe doit comporter {{ limit }} caractères maximum."
    )]
    private ?string $password = null;

    private ?string $firstname = null;

    private ?string $lastname = null;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Email]
    private ?string $email = null;
    private ?string $projectCategoriesInitialisation = null;

    private ?string $taskCategoriesInitialisation = null;
    private ?string $taskStatusesInitialisation = null;

    public function getOrganizationName(): ?string
    {
        return $this->organizationName;
    }

    public function setOrganizationName(?string $organizationName): self
    {
        $this->organizationName = $organizationName;
        return $this;
    }

    public function getOrganizationIdentifier(): ?string
    {
        return $this->organizationIdentifier;
    }

    public function setOrganizationIdentifier(?string $organizationIdentifier): self
    {
        $this->organizationIdentifier = $organizationIdentifier;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getProjectCategoriesInitialisation(): ?string
    {
        return $this->projectCategoriesInitialisation;
    }

    public function setProjectCategoriesInitialisation(?string $projectCategoriesInitialisation): self
    {
        $this->projectCategoriesInitialisation = $projectCategoriesInitialisation;
        return $this;
    }

    public function getTaskCategoriesInitialisation(): ?string
    {
        return $this->taskCategoriesInitialisation;
    }

    public function setTaskCategoriesInitialisation(?string $taskCategoriesInitialisation): self
    {
        $this->taskCategoriesInitialisation = $taskCategoriesInitialisation;
        return $this;
    }

    public function getTaskStatusesInitialisation(): ?string
    {
        return $this->taskStatusesInitialisation;
    }

    public function setTaskStatusesInitialisation(?string $taskStatusesInitialisation): self
    {
        $this->taskStatusesInitialisation = $taskStatusesInitialisation;
        return $this;
    }
}
