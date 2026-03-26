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
    
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $certificateur = null;


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

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $cpfUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lieu = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $bloc = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $publicVise = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $mentionCpf = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $objectifsPedagogiques = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $modalitesCertification = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $tauxReussite = null;

    #[ORM\Column(name: 'date_enregistrement_certification', type: 'string', length: 255, nullable: true)]
    private ?string $dateEnregistrementCertification = null;

    #[ORM\Column(name: 'equivalences', type: 'text', nullable: true)]
    private ?string $equivalencesPasserelles = null;

    #[ORM\Column(name: 'suite_parcours', type: 'text', nullable: true)]
    private ?string $suitesParcours = null;

    #[ORM\Column(name: 'debouches', type: 'text', nullable: true)]
    private ?string $debouchesProfessionnels = null;

    #[ORM\Column(name: 'modalites_acces', type: 'text', nullable: true)]
    private ?string $modalitesAcces = null;

    #[ORM\Column(name: 'accessibilite_handicap', type: 'text', nullable: true)]
    private ?string $accessibilitePSH = null;

    #[ORM\Column(name: 'delai_acces', type: 'text', nullable: true)]
    private ?string $delaisAcces = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

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

     // Getter pour cpfUrl
     public function getCpfUrl(): ?string
     {
         return $this->cpfUrl;
     }
 
     // Setter pour cpfUrl
     public function setCpfUrl(?string $cpfUrl): self
     {
         $this->cpfUrl = $cpfUrl;
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
     public function getCertificateur(): ?string
    {
    return $this->certificateur;
     }

    public function setCertificateur(?string $certificateur): self
    {
    $this->certificateur = $certificateur;
    return $this;
    }

 
     // Setter pour Bloc
     public function setBloc(?string $bloc): self
     {
         $this->bloc = $bloc;
         return $this;
     }

    public function getPublicVise(): ?string
    {
        return $this->publicVise;
    }

    public function setPublicVise(?string $publicVise): self
    {
        $this->publicVise = $publicVise;
        return $this;
    }

    public function getMentionCpf(): ?string
    {
        return $this->mentionCpf;
    }

    public function setMentionCpf(?string $mentionCpf): self
    {
        $this->mentionCpf = $mentionCpf;
        return $this;
    }

    public function getObjectifsPedagogiques(): ?string
    {
        return $this->objectifsPedagogiques;
    }

    public function setObjectifsPedagogiques(?string $objectifsPedagogiques): self
    {
        $this->objectifsPedagogiques = $objectifsPedagogiques;
        return $this;
    }

    public function getModalitesCertification(): ?string
    {
        return $this->modalitesCertification;
    }

    public function setModalitesCertification(?string $modalitesCertification): self
    {
        $this->modalitesCertification = $modalitesCertification;
        return $this;
    }

    public function getTauxReussite(): ?string
    {
        return $this->tauxReussite;
    }

    public function setTauxReussite(?string $tauxReussite): self
    {
        $this->tauxReussite = $tauxReussite;
        return $this;
    }

    public function getDateEnregistrementCertification(): ?string
    {
        return $this->dateEnregistrementCertification;
    }

    public function setDateEnregistrementCertification(?string $dateEnregistrementCertification): self
    {
        $this->dateEnregistrementCertification = $dateEnregistrementCertification;
        return $this;
    }

    public function getEquivalencesPasserelles(): ?string
    {
        return $this->equivalencesPasserelles;
    }

    public function setEquivalencesPasserelles(?string $equivalencesPasserelles): self
    {
        $this->equivalencesPasserelles = $equivalencesPasserelles;
        return $this;
    }

    public function getSuitesParcours(): ?string
    {
        return $this->suitesParcours;
    }

    public function setSuitesParcours(?string $suitesParcours): self
    {
        $this->suitesParcours = $suitesParcours;
        return $this;
    }

    public function getDebouchesProfessionnels(): ?string
    {
        return $this->debouchesProfessionnels;
    }

    public function setDebouchesProfessionnels(?string $debouchesProfessionnels): self
    {
        $this->debouchesProfessionnels = $debouchesProfessionnels;
        return $this;
    }

    public function getModalitesAcces(): ?string
    {
        return $this->modalitesAcces;
    }

    public function setModalitesAcces(?string $modalitesAcces): self
    {
        $this->modalitesAcces = $modalitesAcces;
        return $this;
    }

    public function getAccessibilitePSH(): ?string
    {
        return $this->accessibilitePSH;
    }

    public function setAccessibilitePSH(?string $accessibilitePSH): self
    {
        $this->accessibilitePSH = $accessibilitePSH;
        return $this;
    }

    public function getDelaisAcces(): ?string
    {
        return $this->delaisAcces;
    }

    public function setDelaisAcces(?string $delaisAcces): self
    {
        $this->delaisAcces = $delaisAcces;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
}