<?php

namespace App\Entity;

use App\Repository\AnalyticsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnalyticsRepository::class)]
#[ORM\Table(name: 'analytics')]
#[ORM\Index(name: 'idx_session_id', columns: ['session_id'])]
#[ORM\Index(name: 'idx_visited_at', columns: ['visited_at'])]
#[ORM\Index(name: 'idx_page_url', columns: ['page_url'])]
class Analytics
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $sessionId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $consentToken = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $pageUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $pageTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $referrer = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $device = null; // mobile, tablet, desktop

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $browser = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $operatingSystem = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $timeOnPage = null; // En secondes

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $scrollDepth = null; // Pourcentage (0-100)

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $visitedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $utmSource = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $utmMedium = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $utmCampaign = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isBounce = false; // Visiteur qui part sans interaction

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $customData = null; // Données personnalisées

    // ===== PARCOURS UTILISATEUR =====
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $previousPageUrl = null; // Page précédente dans la session

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $pagesPerSession = null; // Nombre de pages vues dans cette session

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $exitPage = null; // Dernière page avant sortie (NULL si session active)

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $landingPage = null; // Première page de la session

    // ===== NOUVEAUX VS RETOURS =====
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isNewVisitor = true; // Première visite ?

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $sessionCount = null; // Numéro de session pour cet utilisateur

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastVisitDate = null; // Date de la dernière visite (avant celle-ci)

    // ===== ENGAGEMENT =====
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isEngaged = false; // Session engagée (>= 2 pages ou >= 10s + 1 interaction)

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $clickCount = null; // Nombre de clics dans la session

    // ===== RECHERCHE INTERNE =====
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $searchQuery = null; // Terme recherché sur le site

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $searchResultsCount = null; // Nombre de résultats trouvés

    // ===== PERFORMANCE WEB =====
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $pageLoadTime = null; // Temps de chargement complet (ms)

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $domReadyTime = null; // Temps DOM ready (ms)

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $firstPaintTime = null; // First Contentful Paint (ms)

    // ===== DONNÉES TECHNIQUES SUPPLÉMENTAIRES =====
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $screenResolution = null; // Résolution écran (ex: 1920x1080)

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $viewportSize = null; // Taille viewport (ex: 1200x800)

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $languagePreference = null; // Langue navigateur

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $utmTerm = null; // UTM Term (mot-clé)

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $utmContent = null; // UTM Content (variante d'annonce)

    public function __construct()
    {
        $this->visitedAt = new \DateTime();
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

    public function getReferrer(): ?string
    {
        return $this->referrer;
    }

    public function setReferrer(?string $referrer): static
    {
        $this->referrer = $referrer;
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

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): static
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(?string $device): static
    {
        $this->device = $device;
        return $this;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(?string $browser): static
    {
        $this->browser = $browser;
        return $this;
    }

    public function getOperatingSystem(): ?string
    {
        return $this->operatingSystem;
    }

    public function setOperatingSystem(?string $operatingSystem): static
    {
        $this->operatingSystem = $operatingSystem;
        return $this;
    }

    public function getTimeOnPage(): ?int
    {
        return $this->timeOnPage;
    }

    public function setTimeOnPage(?int $timeOnPage): static
    {
        $this->timeOnPage = $timeOnPage;
        return $this;
    }

    public function getScrollDepth(): ?int
    {
        return $this->scrollDepth;
    }

    public function setScrollDepth(?int $scrollDepth): static
    {
        $this->scrollDepth = $scrollDepth;
        return $this;
    }

    public function getVisitedAt(): ?\DateTimeInterface
    {
        return $this->visitedAt;
    }

    public function setVisitedAt(\DateTimeInterface $visitedAt): static
    {
        $this->visitedAt = $visitedAt;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getUtmSource(): ?string
    {
        return $this->utmSource;
    }

    public function setUtmSource(?string $utmSource): static
    {
        $this->utmSource = $utmSource;
        return $this;
    }

    public function getUtmMedium(): ?string
    {
        return $this->utmMedium;
    }

    public function setUtmMedium(?string $utmMedium): static
    {
        $this->utmMedium = $utmMedium;
        return $this;
    }

    public function getUtmCampaign(): ?string
    {
        return $this->utmCampaign;
    }

    public function setUtmCampaign(?string $utmCampaign): static
    {
        $this->utmCampaign = $utmCampaign;
        return $this;
    }

    public function isBounce(): bool
    {
        return $this->isBounce;
    }

    public function setIsBounce(bool $isBounce): static
    {
        $this->isBounce = $isBounce;
        return $this;
    }

    public function getCustomData(): ?array
    {
        return $this->customData;
    }

    public function setCustomData(?array $customData): static
    {
        $this->customData = $customData;
        return $this;
    }

    // ===== GETTERS/SETTERS PARCOURS UTILISATEUR =====
    
    public function getPreviousPageUrl(): ?string
    {
        return $this->previousPageUrl;
    }

    public function setPreviousPageUrl(?string $previousPageUrl): static
    {
        $this->previousPageUrl = $previousPageUrl;
        return $this;
    }

    public function getPagesPerSession(): ?int
    {
        return $this->pagesPerSession;
    }

    public function setPagesPerSession(?int $pagesPerSession): static
    {
        $this->pagesPerSession = $pagesPerSession;
        return $this;
    }

    public function getExitPage(): ?string
    {
        return $this->exitPage;
    }

    public function setExitPage(?string $exitPage): static
    {
        $this->exitPage = $exitPage;
        return $this;
    }

    public function getLandingPage(): ?string
    {
        return $this->landingPage;
    }

    public function setLandingPage(?string $landingPage): static
    {
        $this->landingPage = $landingPage;
        return $this;
    }

    // ===== GETTERS/SETTERS NOUVEAUX VS RETOURS =====
    
    public function isNewVisitor(): bool
    {
        return $this->isNewVisitor;
    }

    public function setIsNewVisitor(bool $isNewVisitor): static
    {
        $this->isNewVisitor = $isNewVisitor;
        return $this;
    }

    public function getSessionCount(): ?int
    {
        return $this->sessionCount;
    }

    public function setSessionCount(?int $sessionCount): static
    {
        $this->sessionCount = $sessionCount;
        return $this;
    }

    public function getLastVisitDate(): ?\DateTimeInterface
    {
        return $this->lastVisitDate;
    }

    public function setLastVisitDate(?\DateTimeInterface $lastVisitDate): static
    {
        $this->lastVisitDate = $lastVisitDate;
        return $this;
    }

    // ===== GETTERS/SETTERS ENGAGEMENT =====
    
    public function isEngaged(): bool
    {
        return $this->isEngaged;
    }

    public function setIsEngaged(bool $isEngaged): static
    {
        $this->isEngaged = $isEngaged;
        return $this;
    }

    public function getClickCount(): ?int
    {
        return $this->clickCount;
    }

    public function setClickCount(?int $clickCount): static
    {
        $this->clickCount = $clickCount;
        return $this;
    }

    // ===== GETTERS/SETTERS RECHERCHE INTERNE =====
    
    public function getSearchQuery(): ?string
    {
        return $this->searchQuery;
    }

    public function setSearchQuery(?string $searchQuery): static
    {
        $this->searchQuery = $searchQuery;
        return $this;
    }

    public function getSearchResultsCount(): ?int
    {
        return $this->searchResultsCount;
    }

    public function setSearchResultsCount(?int $searchResultsCount): static
    {
        $this->searchResultsCount = $searchResultsCount;
        return $this;
    }

    // ===== GETTERS/SETTERS PERFORMANCE WEB =====
    
    public function getPageLoadTime(): ?int
    {
        return $this->pageLoadTime;
    }

    public function setPageLoadTime(?int $pageLoadTime): static
    {
        $this->pageLoadTime = $pageLoadTime;
        return $this;
    }

    public function getDomReadyTime(): ?int
    {
        return $this->domReadyTime;
    }

    public function setDomReadyTime(?int $domReadyTime): static
    {
        $this->domReadyTime = $domReadyTime;
        return $this;
    }

    public function getFirstPaintTime(): ?int
    {
        return $this->firstPaintTime;
    }

    public function setFirstPaintTime(?int $firstPaintTime): static
    {
        $this->firstPaintTime = $firstPaintTime;
        return $this;
    }

    // ===== GETTERS/SETTERS DONNÉES TECHNIQUES =====
    
    public function getScreenResolution(): ?string
    {
        return $this->screenResolution;
    }

    public function setScreenResolution(?string $screenResolution): static
    {
        $this->screenResolution = $screenResolution;
        return $this;
    }

    public function getViewportSize(): ?string
    {
        return $this->viewportSize;
    }

    public function setViewportSize(?string $viewportSize): static
    {
        $this->viewportSize = $viewportSize;
        return $this;
    }

    public function getLanguagePreference(): ?string
    {
        return $this->languagePreference;
    }

    public function setLanguagePreference(?string $languagePreference): static
    {
        $this->languagePreference = $languagePreference;
        return $this;
    }

    public function getUtmTerm(): ?string
    {
        return $this->utmTerm;
    }

    public function setUtmTerm(?string $utmTerm): static
    {
        $this->utmTerm = $utmTerm;
        return $this;
    }

    public function getUtmContent(): ?string
    {
        return $this->utmContent;
    }

    public function setUtmContent(?string $utmContent): static
    {
        $this->utmContent = $utmContent;
        return $this;
    }
}
