<?php

namespace App\Entity;

use App\Repository\TaskCommentFileRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TaskCommentFileRepository::class)]
class TaskCommentFile
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TaskComment::class, cascade: ['persist'], inversedBy: 'files')]
    #[ORM\JoinColumn(name:'task_comment_id', nullable: false)]
    private ?TaskComment $comment = null;

    #[ORM\OneToOne(targetEntity: File::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name:'file_id', nullable: false, onDelete: 'CASCADE')]
    private ?File $file = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?TaskComment
    {
        return $this->comment;
    }

    public function setComment(?TaskComment $comment): self
    {
        $this->comment = $comment;
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
