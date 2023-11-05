<?php

namespace App\Entity;

use App\Repository\ProjectFileRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProjectFileRepository::class)]
class ProjectFile
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Project::class, cascade: ['persist'], inversedBy: 'files')]
    #[ORM\JoinColumn(name:'project_id', nullable: false)]
    private ?Project $project = null;

    #[ORM\OneToOne(targetEntity: File::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name:'file_id', nullable: false, onDelete: 'CASCADE')]
    private ?File $file = null;

    public function getId(): ?int
    {
        return $this->id;
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
