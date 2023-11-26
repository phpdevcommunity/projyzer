<?php

namespace App\Entity;

use App\Repository\TaskFileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TaskFileRepository::class)]
class TaskFile
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, unique: true, nullable: false)]
    private ?string $uid = null;

    #[ORM\ManyToOne(targetEntity: Task::class, cascade: ['persist'], inversedBy: 'files')]
    #[ORM\JoinColumn(name:'task_id', nullable: false)]
    private ?Task $task = null;

    #[ORM\OneToOne(targetEntity: File::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name:'file_id', nullable: false, onDelete: 'CASCADE')]
    private ?File $file = null;

    public function __construct()
    {
        $this->uid = bin2hex(random_bytes(64));
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(?string $uid): self
    {
        $this->uid = $uid;
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

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;
        return $this;
    }
}
