<?php

namespace App\Entity;

use App\Repository\AnalyticsEventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnalyticsEventRepository::class)]
#[ORM\Table(name: 'analytics_event')]
#[ORM\Index(name: 'idx_event_session', columns: ['session_id'])]
#[ORM\Index(name: 'idx_event_name', columns: ['event_name'])]
#[ORM\Index(name: 'idx_event_date', columns: ['event_date'])]
class AnalyticsEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $sessionId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $consentToken = null;

    #[ORM\Column(length: 255)]
    private ?string $eventName = null; // e.g., 'calendly_click', 'pdf_download'

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $eventCategory = null; // e.g., 'conversion', 'engagement'

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $eventLabel = null; // e.g., 'Formation X - Bouton Y'

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $eventValue = null; // e.g., price of a conversion

    #[ORM\Column(type: Types::TEXT)]
    private ?string $pageUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $pageTitle = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $eventData = null; // Custom JSON data

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $eventDate = null;

    public function __construct()
    {
        $this->eventDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): static
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    public function getConsentToken(): ?string
    {
        return $this->consentToken;
    }

    public function setConsentToken(?string $consentToken): static
    {
        $this->consentToken = $consentToken;
        return $this;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): static
    {
        $this->eventName = $eventName;
        return $this;
    }

    public function getEventCategory(): ?string
    {
        return $this->eventCategory;
    }

    public function setEventCategory(?string $eventCategory): static
    {
        $this->eventCategory = $eventCategory;
        return $this;
    }

    public function getEventLabel(): ?string
    {
        return $this->eventLabel;
    }

    public function setEventLabel(?string $eventLabel): static
    {
        $this->eventLabel = $eventLabel;
        return $this;
    }

    public function getEventValue(): ?int
    {
        return $this->eventValue;
    }

    public function setEventValue(?int $eventValue): static
    {
        $this->eventValue = $eventValue;
        return $this;
    }

    public function getPageUrl(): ?string
    {
        return $this->pageUrl;
    }

    public function setPageUrl(string $pageUrl): static
    {
        $this->pageUrl = $pageUrl;
        return $this;
    }

    public function getPageTitle(): ?string
    {
        return $this->pageTitle;
    }

    public function setPageTitle(?string $pageTitle): static
    {
        $this->pageTitle = $pageTitle;
        return $this;
    }

    public function getEventData(): ?array
    {
        return $this->eventData;
    }

    public function setEventData(?array $eventData): static
    {
        $this->eventData = $eventData;
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

    public function getEventDate(): ?\DateTimeInterface
    {
        return $this->eventDate;
    }

    public function setEventDate(\DateTimeInterface $eventDate): static
    {
        $this->eventDate = $eventDate;
        return $this;
    }
}












