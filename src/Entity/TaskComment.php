<?php

namespace App\Entity;

use App\Repository\TaskCommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TaskCommentRepository::class)]
class TaskComment
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: Task::class, cascade: ['persist'], inversedBy: 'comments')]
    #[ORM\JoinColumn(name:'task_id', nullable: false)]
    private ?Task $task = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name:'user_id', nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: "comment", targetEntity: TaskCommentFile::class, cascade: ['persist', 'remove'])]
    private iterable $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
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

    public function getFiles(): iterable
    {
        return $this->files;
    }

    public function addFile(TaskCommentFile $file): self
    {
        $file->setComment($this);
        if (!$this->files->contains($file)) {
            $this->files->add($file);
        }
        return $this;
    }
}
