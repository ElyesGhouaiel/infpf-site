<?php

namespace App\Controller;

use App\Entity\CookieConsent;
use App\Entity\Analytics;
use App\Entity\AnalyticsEvent;
use App\Repository\CookieConsentRepository;
use App\Repository\AnalyticsRepository;
use App\Repository\AnalyticsEventRepository;
use App\Service\AnalyticsExclusionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cookie')]
class CookieConsentController extends AbstractController
{
    private AnalyticsExclusionService $exclusionService;

    public function __construct(AnalyticsExclusionService $exclusionService)
    {
        $this->exclusionService = $exclusionService;
    }
    /**
     * Enregistrer le consentement de l'utilisateur
     */
    #[Route('/consent', name: 'app_cookie_consent', methods: ['POST'])]
    public function saveConsent(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        
        $consent = new CookieConsent();
        $consent->setConsentToken($token);
        $clientIp = $this->getClientIp($request);
        $consent->setIpAddress($this->exclusionService->anonymizeIp($clientIp));
        $consent->setUserAgent($request->headers->get('User-Agent'));
        $consent->setNecessaryCookies(true); // Toujours true
        $consent->setAnalyticsCookies($data['analytics'] ?? false);
        $consent->setMarketingCookies($data['marketing'] ?? false);
        
        $em->persist($consent);
        $em->flush();
        
        return new JsonResponse([
            'success' => true,
            'token' => $token,
            'message' => 'Consentement enregistré avec succès'
        ]);
    }
    
    /**
     * Mettre à jour le consentement
     */
    #[Route('/consent/update', name: 'app_cookie_consent_update', methods: ['POST'])]
    public function updateConsent(Request $request, EntityManagerInterface $em, CookieConsentRepository $repo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;
        
        if (!$token) {
            return new JsonResponse(['success' => false, 'error' => 'Token manquant'], 400);
        }
        
        $consent = $repo->findByToken($token);
        
        if (!$consent) {
            return new JsonResponse(['success' => false, 'error' => 'Consentement introuvable'], 404);
        }
        
        $consent->setAnalyticsCookies($data['analytics'] ?? false);
        $consent->setMarketingCookies($data['marketing'] ?? false);
        $consent->setUpdatedAt(new \DateTime());
        
        $em->flush();
        
        return new JsonResponse([
            'success' => true,
            'message' => 'Consentement mis à jour'
        ]);
    }
    
    /**
     * Enregistrer une visite analytics
     */
    #[Route('/track', name: 'app_analytics_track', methods: ['POST'])]
    public function track(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // ===== 1. VÉRIFIER LES EXCLUSIONS =====
        $exclusion = $this->exclusionService->shouldExclude($request);
        if ($exclusion['excluded']) {
            return new JsonResponse([
                'success' => true,
                'excluded' => true,
                'reason' => $exclusion['reason'],
                'message' => 'Trafic exclu du tracking'
            ]);
        }
        
        $data = json_decode($request->getContent(), true);
        
        // ===== 2. VÉRIFIER LE CONSENTEMENT =====
        $consentToken = $data['consentToken'] ?? null;
        $anonymousMode = $data['anonymousMode'] ?? false;
        
        // Mode anonyme strict (tout refuser) - Agrégation uniquement, pas de stockage individuel
        if ($anonymousMode) {
            // En mode anonyme, on ne stocke RIEN individuellement
            // Juste un comptage agrégé (qui sera fait côté frontend ou via un autre mécanisme)
            return new JsonResponse([
                'success' => true,
                'anonymous' => true,
                'message' => 'Mode anonyme - pas de tracking individuel'
            ]);
        }
        
        // Mode avec consentement - Tracking complet
        if (!$consentToken) {
            return new JsonResponse(['success' => false, 'error' => 'Pas de consentement'], 403);
        }
        
        // ===== 3. CRÉER L'ENTRÉE ANALYTICS =====
        $analytics = new Analytics();
        $analytics->setSessionId($data['sessionId'] ?? session_id());
        $analytics->setConsentToken($consentToken);
        $analytics->setPageUrl($data['pageUrl'] ?? '');
        $analytics->setPageTitle($data['pageTitle'] ?? '');
        $analytics->setReferrer($data['referrer'] ?? null);
        $analytics->setUserAgent($request->headers->get('User-Agent'));
        
        // IP : toujours anonymisée
        $clientIp = $this->getClientIp($request);
        $analytics->setIpAddress($this->exclusionService->anonymizeIp($clientIp));
        
        // Détection device/browser/OS
        $analytics->setDevice($this->detectDevice($request->headers->get('User-Agent')));
        $analytics->setBrowser($this->detectBrowser($request->headers->get('User-Agent')));
        $analytics->setOperatingSystem($this->detectOS($request->headers->get('User-Agent')));
        
        // Temps sur la page et scroll depth
        $analytics->setTimeOnPage($data['timeOnPage'] ?? null);
        $analytics->setScrollDepth($data['scrollDepth'] ?? null);
        
        // Paramètres UTM (tous les paramètres maintenant)
        $analytics->setUtmSource($data['utmSource'] ?? null);
        $analytics->setUtmMedium($data['utmMedium'] ?? null);
        $analytics->setUtmCampaign($data['utmCampaign'] ?? null);
        $analytics->setUtmTerm($data['utmTerm'] ?? null);
        $analytics->setUtmContent($data['utmContent'] ?? null);
        
        // Géolocalisation serveur (pays uniquement, IP non stockée)
        $country = $this->exclusionService->getCountryFromIp($clientIp);
        $analytics->setCountry($country ?? $data['country'] ?? null);
        $analytics->setCity(null); // On ne collecte pas la ville (trop précis pour RGPD)
        
        // Bounce et engagement
        $analytics->setIsBounce($data['isBounce'] ?? false);
        $analytics->setIsEngaged($data['isEngaged'] ?? false);
        
        // ===== NOUVELLES DONNÉES PRO =====
        
        // Parcours utilisateur
        $analytics->setPreviousPageUrl($data['previousPageUrl'] ?? null);
        $analytics->setLandingPage($data['landingPage'] ?? null);
        $analytics->setPagesPerSession($data['pagesPerSession'] ?? 1);
        $analytics->setExitPage(null); // Sera mis à jour lors de la sortie
        
        // Nouveaux vs Retours
        $isNewVisitor = $this->isNewVisitor($request, $data['userId'] ?? null);
        $analytics->setIsNewVisitor($isNewVisitor);
        $analytics->setSessionCount($data['sessionCount'] ?? 1);
        $analytics->setLastVisitDate(null); // TODO: implémenter si nécessaire
        
        // Engagement
        $analytics->setClickCount($data['clickCount'] ?? 0);
        
        // Recherche interne
        $analytics->setSearchQuery($data['searchQuery'] ?? null);
        $analytics->setSearchResultsCount($data['searchResultsCount'] ?? null);
        
        // Performance web
        $analytics->setPageLoadTime($data['pageLoadTime'] ?? null);
        $analytics->setDomReadyTime($data['domReadyTime'] ?? null);
        $analytics->setFirstPaintTime($data['firstPaintTime'] ?? null);
        
        // Données techniques supplémentaires
        $analytics->setScreenResolution($data['screenResolution'] ?? null);
        $analytics->setViewportSize($data['viewportSize'] ?? null);
        $analytics->setLanguagePreference($data['language'] ?? null);
        
        // Données personnalisées (événements)
        $analytics->setCustomData($data['customData'] ?? null);
        
        $em->persist($analytics);
        $em->flush();
        
        return new JsonResponse([
            'success' => true,
            'message' => 'Visite enregistrée'
        ]);
    }
    
    /**
     * Récupère l'IP réelle du client (gère les proxies/CDN)
     */
    private function getClientIp(Request $request): ?string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',  // Cloudflare
            'HTTP_X_REAL_IP',         // Nginx proxy
            'HTTP_X_FORWARDED_FOR',   // Standard proxy
        ];

        foreach ($headers as $header) {
            $ip = $request->server->get($header);
            if ($ip) {
                // X-Forwarded-For peut contenir plusieurs IPs
                if (str_contains($ip, ',')) {
                    $ips = array_map('trim', explode(',', $ip));
                    $ip = $ips[0]; // Prendre la première (IP cliente)
                }
                return $ip;
            }
        }

        return $request->getClientIp();
    }
    
    /**
     * Enregistrer un événement (scroll, clic, sortie de page) - Mise à jour des métriques
     */
    #[Route('/event', name: 'app_analytics_event', methods: ['POST'])]
    public function trackEvent(Request $request, EntityManagerInterface $em, AnalyticsRepository $repo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $sessionId = $data['sessionId'] ?? null;
        $pageUrl = $data['pageUrl'] ?? null;
        
        if (!$sessionId || !$pageUrl) {
            return new JsonResponse(['success' => false], 400);
        }
        
        // Récupérer la dernière visite pour cette session/page
        $analytics = $repo->findOneBy([
            'sessionId' => $sessionId,
            'pageUrl' => $pageUrl
        ], ['visitedAt' => 'DESC']);
        
        if ($analytics) {
            // Mettre à jour temps sur page et scroll depth
            if (isset($data['timeOnPage'])) {
                $analytics->setTimeOnPage($data['timeOnPage']);
            }
            if (isset($data['scrollDepth'])) {
                $analytics->setScrollDepth($data['scrollDepth']);
            }
            if (isset($data['isBounce'])) {
                $analytics->setIsBounce($data['isBounce']);
            }
            if (isset($data['isEngaged'])) {
                $analytics->setIsEngaged($data['isEngaged']);
            }
            if (isset($data['clickCount'])) {
                $analytics->setClickCount($data['clickCount']);
            }
            if (isset($data['pagesPerSession'])) {
                $analytics->setPagesPerSession($data['pagesPerSession']);
            }
            
            $em->flush();
        }
        
        return new JsonResponse(['success' => true]);
    }
    
    /**
     * Enregistrer un événement personnalisé (conversion, engagement, etc.)
     */
    #[Route('/event/custom', name: 'app_analytics_event_custom', methods: ['POST'])]
    public function trackCustomEvent(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Vérifier les exclusions
        $exclusion = $this->exclusionService->shouldExclude($request);
        if ($exclusion['excluded']) {
            return new JsonResponse([
                'success' => true,
                'excluded' => true,
                'reason' => $exclusion['reason']
            ]);
        }
        
        $data = json_decode($request->getContent(), true);
        
        // Vérifier le consentement
        $consentToken = $data['consentToken'] ?? null;
        if (!$consentToken) {
            return new JsonResponse(['success' => false, 'error' => 'Pas de consentement'], 403);
        }
        
        $event = new AnalyticsEvent();
        $event->setSessionId($data['sessionId'] ?? 'unknown');
        $event->setConsentToken($consentToken);
        $event->setEventName($data['eventName'] ?? 'unknown_event');
        $event->setEventCategory($data['eventCategory'] ?? 'engagement');
        $event->setEventLabel($data['eventLabel'] ?? null);
        $event->setEventValue($data['eventValue'] ?? null);
        $event->setPageUrl($data['pageUrl'] ?? '');
        $event->setPageTitle($data['pageTitle'] ?? '');
        $event->setEventData($data['eventData'] ?? null);
        
        // IP et User Agent
        $clientIp = $this->getClientIp($request);
        $event->setIpAddress($this->exclusionService->anonymizeIp($clientIp));
        $event->setUserAgent($request->headers->get('User-Agent'));
        
        $em->persist($event);
        $em->flush();
        
        return new JsonResponse([
            'success' => true,
            'message' => 'Événement enregistré'
        ]);
    }
    
    /**
     * Détecter le type d'appareil
     */
    private function detectDevice(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }
        
        if (preg_match('/mobile|android|iphone|ipod|blackberry|iemobile/i', $userAgent)) {
            return 'mobile';
        }
        
        if (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }
    
    /**
     * Détecter le navigateur
     */
    private function detectBrowser(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }
        
        $browsers = [
            'Chrome' => '/chrome/i',
            'Firefox' => '/firefox/i',
            'Safari' => '/safari/i',
            'Edge' => '/edge/i',
            'Opera' => '/opera|opr/i',
            'Internet Explorer' => '/msie|trident/i'
        ];
        
        foreach ($browsers as $browser => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $browser;
            }
        }
        
        return 'Other';
    }
    
    /**
     * Détecter le système d'exploitation
     */
    private function detectOS(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }
        
        $os = [
            'Windows' => '/windows/i',
            'macOS' => '/mac os x/i',
            'Linux' => '/linux/i',
            'Android' => '/android/i',
            'iOS' => '/iphone|ipad|ipod/i'
        ];
        
        foreach ($os as $system => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $system;
            }
        }
        
        return 'Other';
    }
    
    /**
     * Déterminer si c'est un nouveau visiteur
     * Basé sur le userId envoyé côté client (cookie long terme)
     */
    private function isNewVisitor(Request $request, ?string $userId): bool
    {
        if (!$userId) {
            return true; // Pas d'userId = nouveau visiteur
        }
        
        // Le frontend génère un userId qui persiste 2 ans
        // Si cet ID existe dans nos analytics, c'est un visiteur qui revient
        // Cette logique pourrait être améliorée en vérifiant la DB
        // Pour l'instant, on se fie au frontend
        
        return false; // Si userId existe, c'est un visiteur qui revient
    }
}

