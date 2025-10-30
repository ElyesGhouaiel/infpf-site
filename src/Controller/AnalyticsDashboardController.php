<?php

namespace App\Controller;

use App\Repository\AnalyticsRepository;
use App\Repository\AnalyticsEventRepository;
use App\Repository\CookieConsentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/analytics')]
#[IsGranted('ROLE_ADMIN')]
class AnalyticsDashboardController extends AbstractController
{
    #[Route('/', name: 'app_analytics_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/analytics/dashboard.html.twig');
    }
    
    #[Route('/data', name: 'app_analytics_data', methods: ['GET'])]
    public function getData(
        Request $request,
        AnalyticsRepository $analyticsRepo,
        AnalyticsEventRepository $eventRepo,
        CookieConsentRepository $consentRepo
    ): JsonResponse {
        // Récupérer la période demandée
        $period = $request->query->get('period', 'today');
        $days = $this->getPeriodDays($period);
        
        // ===== STATISTIQUES GÉNÉRALES =====
        $generalStats = $analyticsRepo->getGeneralStats($days);
        
        // ===== CONSENTEMENTS =====
        $consentStats = $consentRepo->countByType();
        $consentsByDay = $consentRepo->getConsentStatsByDay($days);
        
        // ===== PAGES =====
        $topPages = $analyticsRepo->getTopPages(15, $days);
        
        // ===== TRAFIC =====
        $trafficSources = $analyticsRepo->getTrafficSources(20, $days);
        
        // ===== DEVICES & NAVIGATEURS =====
        $deviceStats = $analyticsRepo->getDeviceStats($days);
        $browserStats = $analyticsRepo->getBrowserStats($days);
        
        // ===== ÉVOLUTION TEMPORELLE =====
        $visitsByDay = $analyticsRepo->getVisitsByDay($days);
        $visitsByHour = $analyticsRepo->getVisitsByHour($days);
        
        // ===== GÉOLOCALISATION =====
        $topCountries = $analyticsRepo->getTopCountries(10, $days);
        
        // ===== CAMPAGNES UTM =====
        $topCampaigns = $analyticsRepo->getTopCampaigns(10, $days);
        
        // ===== NOUVELLES STATS PRO =====
        
        // Nouveaux vs Retours
        $newVsReturning = $analyticsRepo->getNewVsReturningVisitors($days);
        $retentionRate = $analyticsRepo->getRetentionRate($days);
        
        // Engagement
        $engagementRate = $analyticsRepo->getEngagementRate($days);
        $avgPagesPerSession = $analyticsRepo->getAveragePagesPerSession($days);
        
        // Parcours utilisateur
        $topLandingPages = $analyticsRepo->getTopLandingPages(10, $days);
        $topExitPages = $analyticsRepo->getTopExitPages(10, $days);
        $topUserFlows = $analyticsRepo->getTopUserFlows(15, $days);
        
        // Recherche interne
        $topSearchQueries = $analyticsRepo->getTopSearchQueries(20, $days);
        $searchesWithNoResults = $analyticsRepo->getSearchesWithNoResults(15, $days);
        
        // Performance Web
        $performanceStats = $analyticsRepo->getAveragePerformance($days);
        $slowestPages = $analyticsRepo->getSlowestPages(10, $days);
        
        // Distributions
        $timeDistribution = $analyticsRepo->getTimeOnSiteDistribution($days);
        $scrollDistribution = $analyticsRepo->getScrollDepthDistribution($days);
        $sessionFrequency = $analyticsRepo->getSessionFrequency($days);
        
        // Événements
        $topEvents = $eventRepo->getTopEvents(20, $days);
        $eventsByCategory = $eventRepo->getEventsByCategory($days);
        $conversions = $eventRepo->getConversions($days);
        $totalEvents = $eventRepo->getTotalEvents($days);
        
        return new JsonResponse([
            'success' => true,
            'period' => $period,
            'data' => [
                // Stats de base
                'generalStats' => $generalStats,
                'consentStats' => $consentStats,
                'topPages' => $topPages,
                'trafficSources' => $trafficSources,
                'deviceStats' => $deviceStats,
                'browserStats' => $browserStats,
                'visitsByDay' => $visitsByDay,
                'visitsByHour' => $visitsByHour,
                'topCountries' => $topCountries,
                'topCampaigns' => $topCampaigns,
                'consentsByDay' => $consentsByDay,
                
                // ===== NOUVELLES STATS PRO =====
                'newVsReturning' => $newVsReturning,
                'retentionRate' => $retentionRate,
                'engagementRate' => $engagementRate,
                'avgPagesPerSession' => $avgPagesPerSession,
                'topLandingPages' => $topLandingPages,
                'topExitPages' => $topExitPages,
                'topUserFlows' => $topUserFlows,
                'topSearchQueries' => $topSearchQueries,
                'searchesWithNoResults' => $searchesWithNoResults,
                'performanceStats' => $performanceStats,
                'slowestPages' => $slowestPages,
                'timeDistribution' => $timeDistribution,
                'scrollDistribution' => $scrollDistribution,
                'sessionFrequency' => $sessionFrequency,
                'topEvents' => $topEvents,
                'eventsByCategory' => $eventsByCategory,
                'conversions' => $conversions,
                'totalEvents' => $totalEvents,
            ],
            'timestamp' => time(),
        ]);
    }
    
    private function getPeriodDays(string $period): int
    {
        return match($period) {
            'today' => 1,
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 1,
        };
    }
}

