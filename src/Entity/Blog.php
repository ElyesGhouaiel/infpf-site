<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_PUBLISHED = 'published';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title_one = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(length: 20, nullable: false)]
    private string $status = 'draft';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $author = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content_one = null;
    
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDesc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title_two = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content_two = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title_tree = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content_tree = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sous_title_tree = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sous_content_tree = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title_for = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content_for = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title_five = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content_five = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: Comment::class, cascade: ['persist', 'remove'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: BlogSection::class, cascade: ['persist', 'remove'])]
    private Collection $dynamicSections;


    public function __construct() {
        $this->comments = new ArrayCollection();
        $this->dynamicSections = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTitleOne(): ?string
    {
        return $this->title_one;
    }
    

    public function setTitleOne(?string $title_one): static
    {
        $this->title_one = $title_one;

        return $this;
    }
    
    public function getShortDesc(){
        return $this->shortDesc;
    }
    
    public function setShortDesc(?string $shortDesc){
        $this->shortDesc = $shortDesc;
        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getContentOne(): ?string
    {
        return $this->content_one;
    }

    public function setContentOne(?string $content_one): static
    {
        $this->content_one = $content_one;

        return $this;
    }

    public function getTitleTwo(): ?string
    {
        return $this->title_two;
    }

    public function setTitleTwo(?string $title_two): static
    {
        $this->title_two = $title_two;

        return $this;
    }

    public function getContentTwo(): ?string
    {
        return $this->content_two;
    }

    public function setContentTwo(?string $content_two): static
    {
        $this->content_two = $content_two;

        return $this;
    }

    public function getTitleTree(): ?string
    {
        return $this->title_tree;
    }

    public function setTitleTree(?string $title_tree): static
    {
        $this->title_tree = $title_tree;

        return $this;
    }

    public function getContentTree(): ?string
    {
        return $this->content_tree;
    }

    public function setContentTree(?string $content_tree): static
    {
        $this->content_tree = $content_tree;

        return $this;
    }

    public function getSousTitleTree(): ?string
    {
        return $this->sous_title_tree;
    }

    public function setSousTitleTree(?string $sous_title_tree): static
    {
        $this->sous_title_tree = $sous_title_tree;

        return $this;
    }

    public function getSousContentTree(): ?string
    {
        return $this->sous_content_tree;
    }

    public function setSousContentTree(?string $sous_content_tree): static
    {
        $this->sous_content_tree = $sous_content_tree;

        return $this;
    }

    public function getTitleFor(): ?string
    {
        return $this->title_for;
    }

    public function setTitleFor(?string $title_for): static
    {
        $this->title_for = $title_for;

        return $this;
    }

    public function getContentFor(): ?string
    {
        return $this->content_for;
    }

    public function setContentFor(?string $content_for): static
    {
        $this->content_for = $content_for;

        return $this;
    }

    public function getTitleFive(): ?string
    {
        return $this->title_five;
    }

    public function setTitleFive(?string $title_five): static
    {
        $this->title_five = $title_five;

        return $this;
    }

    public function getContentFive(): ?string
    {
        return $this->content_five;
    }

    public function setContentFive(?string $content_five): static
    {
        $this->content_five = $content_five;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }


        
    // Getter et setter pour comments
    public function getComments(): Collection {
        return $this->comments;
    }

    public function addComment(Comment $comment): self {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setBlog($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBlog() === $this) {
                $comment->setBlog(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlogSection>
     */
    public function getDynamicSections(): Collection
    {
        return $this->dynamicSections;
    }

    public function addDynamicSection(BlogSection $dynamicSection): static
    {
        if (!$this->dynamicSections->contains($dynamicSection)) {
            $this->dynamicSections->add($dynamicSection);
            $dynamicSection->setBlog($this);
        }

        return $this;
    }

    public function removeDynamicSection(BlogSection $dynamicSection): static
    {
        if ($this->dynamicSections->removeElement($dynamicSection)) {
            // set the owning side to null (unless already changed)
            if ($dynamicSection->getBlog() === $this) {
                $dynamicSection->setBlog(null);
            }
        }

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_SCHEDULED => 'Programmé',
            self::STATUS_PUBLISHED => 'Publié',
            default => 'Inconnu'
        };
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && 
               $this->publishedAt !== null && 
               $this->publishedAt <= new \DateTimeImmutable();
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED && 
               $this->publishedAt !== null && 
               $this->publishedAt > new \DateTimeImmutable();
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }
}