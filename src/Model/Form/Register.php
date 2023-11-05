<?php

namespace App\Model\Form;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Register
 * @package App\Model
 */
class Register
{
    #[Assert\NotBlank]
    private ?string $username = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: "Le mot de passe doit comporter {{ limit }} caractères minimum.",
        maxMessage: "Le mot de passe doit comporter {{ limit }} caractères maximum."
    )]
    private ?string $password = null;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
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
