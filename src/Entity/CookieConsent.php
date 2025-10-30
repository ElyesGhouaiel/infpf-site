<?php

namespace App\Entity;

use App\Repository\CookieConsentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CookieConsentRepository::class)]
#[ORM\Table(name: 'cookie_consent')]
#[ORM\Index(name: 'idx_consent_token', columns: ['consent_token'])]
#[ORM\Index(name: 'idx_created_at', columns: ['created_at'])]
class CookieConsent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $consentToken = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $necessaryCookies = true; // Toujours true (obligatoires)

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $analyticsCookies = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $marketingCookies = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expiresAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $consentVersion = null; // Version de la politique de cookies

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->consentVersion = '1.0'; // Version actuelle de votre politique
        // Expiration après 13 mois (conformité CNIL)
        $this->expiresAt = (new \DateTime())->modify('+13 months');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConsentToken(): ?string
    {
        return $this->consentToken;
    }

    public function setConsentToken(string $consentToken): static
    {
        $this->consentToken = $consentToken;
        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): static
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): static
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function isNecessaryCookies(): bool
    {
        return $this->necessaryCookies;
    }

    public function setNecessaryCookies(bool $necessaryCookies): static
    {
        $this->necessaryCookies = $necessaryCookies;
        return $this;
    }

    public function isAnalyticsCookies(): bool
    {
        return $this->analyticsCookies;
    }

    public function setAnalyticsCookies(bool $analyticsCookies): static
    {
        $this->analyticsCookies = $analyticsCookies;
        return $this;
    }

    public function isMarketingCookies(): bool
    {
        return $this->marketingCookies;
    }

    public function setMarketingCookies(bool $marketingCookies): static
    {
        $this->marketingCookies = $marketingCookies;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $expiresAt): static
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getConsentVersion(): ?string
    {
        return $this->consentVersion;
    }

    public function setConsentVersion(?string $consentVersion): static
    {
        $this->consentVersion = $consentVersion;
        return $this;
    }

    public function isExpired(): bool
    {
        if (!$this->expiresAt) {
            return false;
        }
        return $this->expiresAt < new \DateTime();
    }
}


