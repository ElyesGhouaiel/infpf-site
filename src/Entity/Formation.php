<?php
// src/Entity/Formation.php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFormation = null;

    #[ORM\Column(type: 'text', length: 5000, nullable: true)]
    private ?string $descriptionFormation = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $dureeFormation = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $priceFormation = null; // Le prix en euros

    #[ORM\ManyToOne(inversedBy: 'formations', targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $phraseOne = null;

    #[ORM\Column(type: 'text', length: 5000,nullable: true)]
    private ?string $presentation = null;

    #[ORM\Column(type: 'text', length: 5000, nullable: true)]
    private ?string $prerequis = null;

    #[ORM\Column(type: 'text',length: 5000, nullable: true)]
    private ?string $atouts = null;

    #[ORM\Column(type: 'text', length: 5000, nullable: true)]
    private ?string $modalitesPedagogique = null;

    #[ORM\Column(type: 'text',length: 5000, nullable: true)]
    private ?string $evaluation = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $niveau = null; // Niveau de la formation

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $langue = null;

    #[ORM\Column(type: 'text', length: 50000, nullable: true)]
    private ?string $programme = null; 

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $rncp = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lieu = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $bloc = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameFormation(): ?string
    {
        return $this->nameFormation;
    }

    public function setNameFormation(?string $nameFormation): self
    {
        $this->nameFormation = $nameFormation;
        return $this;
    }

    public function getDescriptionFormation(): ?string
    {
        return $this->descriptionFormation;
    }

    public function setDescriptionFormation(string $descriptionFormation): self
    {
        $this->descriptionFormation = $descriptionFormation;
        return $this;
    }

    public function getDureeFormation(): ?string
    {
        return $this->dureeFormation;
    }

    public function setDureeFormation(string $dureeFormation): self
    {
        $this->dureeFormation = $dureeFormation;
        return $this;
    }

    public function getPriceFormation(): ?int
    {
        return $this->priceFormation;
    }

    public function setPriceFormation(int $priceFormation): self
    {
        $this->priceFormation = $priceFormation;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getPhraseOne(): ?string
    {
        return $this->phraseOne;
    }

    public function setPhraseOne(?string $phraseOne): static
    {
        $this->phraseOne = $phraseOne;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): static
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getPrerequis(): ?string
    {
        return $this->prerequis;
    }

    public function setPrerequis(?string $prerequis): static
    {
        $this->prerequis = $prerequis;

        return $this;
    }

    public function getAtouts(): ?string
    {
        return $this->atouts;
    }

    public function setAtouts(?string $atouts): static
    {
        $this->atouts = $atouts;

        return $this;
    }

    public function getModalitesPedagogique(): ?string
    {
        return $this->modalitesPedagogique;
    }

    public function setModalitesPedagogique(?string $modalitesPedagogique): static
    {
        $this->modalitesPedagogique = $modalitesPedagogique;

        return $this;
    }

    public function getEvaluation(): ?string
    {
        return $this->evaluation;
    }

    public function setEvaluation(?string $evaluation): static
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(?string $niveau): self
    {
        $this->niveau = $niveau;
        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(?string $langue): self
    {
        $this->langue = $langue;
        return $this;
    }

    public function getProgramme(): ?string
    {
        return $this->programme;
    }

    public function setProgramme(?string $programme): static
    {
        $this->programme = $programme;

        return $this;
    }

     // Getter pour RNCP
     public function getRncp(): ?string
     {
         return $this->rncp;
     }
 
     // Setter pour RNCP
     public function setRncp(?string $rncp): self
     {
         $this->rncp = $rncp;
         return $this;
     }
 
     // Getter pour Lieu
     public function getLieu(): ?string
     {
         return $this->lieu;
     }
 
     // Setter pour Lieu
     public function setLieu(?string $lieu): self
     {
         $this->lieu = $lieu;
         return $this;
     }
 
     // Getter pour Bloc
     public function getBloc(): ?string
     {
         return $this->bloc;
     }
 
     // Setter pour Bloc
     public function setBloc(?string $bloc): self
     {
         $this->bloc = $bloc;
         return $this;
     }
}