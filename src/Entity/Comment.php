<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\CommentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $text = null;

    #[ORM\ManyToOne(targetEntity: Blog::class, inversedBy: 'comments')]
    private ?Blog $blog = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }
    public function getBlog(): ?Blog {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): self {
        $this->blog = $blog;
        return $this;
    }
}