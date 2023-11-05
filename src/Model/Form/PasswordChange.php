<?php

namespace App\Model\Form;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordChange
{
    #[Assert\NotBlank]
    private ?string $currentPassword = null;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: "Le mot de passe doit comporter {{ limit }} caractères minimum.",
        maxMessage: "Le mot de passe doit comporter {{ limit }} caractères maximum."
    )]
    private ?string $password = null;

    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword(string $currentPassword): self
    {
        $this->currentPassword = $currentPassword;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}