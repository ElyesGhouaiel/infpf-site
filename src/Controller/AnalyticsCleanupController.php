<?php

namespace App\Controller;

use App\Repository\AnalyticsRepository;
use App\Service\AnalyticsExclusionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/analytics')]
#[IsGranted('ROLE_ADMIN')]
class AnalyticsCleanupController extends AbstractController
{
    /**
     * Page de diagnostic : vérifier si le trafic actuel est exclu
     */
    #[Route('/diagnostic', name: 'app_analytics_diagnostic')]
    public function diagnostic(
        Request $request,
        AnalyticsExclusionService $exclusionService
    ): Response {
        $exclusion = $exclusionService->shouldExclude($request);
        $config = $exclusionService->getConfig();
        
        // Récupérer l'IP réelle
        $clientIp = $this->getClientIp($request);
        $anonymizedIp = $exclusionService->anonymizeIp($clientIp);
        
        return $this->render('admin/analytics/diagnostic.html.twig', [
            'isExcluded' => $exclusion['excluded'],
            'excludeReason' => $exclusion['reason'],
            'clientIp' => $clientIp,
            'anonymizedIp' => $anonymizedIp,
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'currentPath' => $request->getPathInfo(),
            'config' => $config,
        ]);
    }
    
    /**
     * Page de diagnostic BDD : voir les données dans les tables
     */
    #[Route('/diagnostic-bdd', name: 'app_analytics_diagnostic_bdd')]
    public function diagnosticBdd(
        AnalyticsRepository $analyticsRepo,
        EntityManagerInterface $em
    ): Response {
        // Compter les consentements
        $consentRepo = $em->getRepository(\App\Entity\CookieConsent::class);
        $consentTotal = $consentRepo->count([]);
        $consentAnalytics = $consentRepo->count(['analyticsCookies' => true]);
        $consentMarketing = $consentRepo->count(['marketingCookies' => true]);
        
        // Derniers consentements
        $lastConsents = $consentRepo->findBy([], ['createdAt' => 'DESC'], 10);
        
        // Compter les analytics
        $analyticsTotal = $analyticsRepo->count([]);
        
        // Dernières visites
        $lastVisits = $analyticsRepo->findBy([], ['visitedAt' => 'DESC'], 10);
        
        // Pages /admin trackées (ne devrait pas y en avoir)
        $qb = $em->createQueryBuilder();
        $adminTracked = $qb->select('COUNT(a.id)')
            ->from(\App\Entity\Analytics::class, 'a')
            ->where('a.pageUrl LIKE :admin')
            ->setParameter('admin', '/admin%')
            ->getQuery()
            ->getSingleScalarResult();
        
        return $this->render('admin/analytics/diagnostic_bdd.html.twig', [
            'consentTotal' => $consentTotal,
            'consentAnalytics' => $consentAnalytics,
            'consentMarketing' => $consentMarketing,
            'lastConsents' => $lastConsents,
            'analyticsTotal' => $analyticsTotal,
            'lastVisits' => $lastVisits,
            'adminTracked' => $adminTracked,
        ]);
    }
    
    /**
     * API : Vérifier le statut d'exclusion (pour AJAX)
     */
    #[Route('/check-exclusion', name: 'app_analytics_check_exclusion', methods: ['GET'])]
    public function checkExclusion(
        Request $request,
        AnalyticsExclusionService $exclusionService
    ): JsonResponse {
        $exclusion = $exclusionService->shouldExclude($request);
        $clientIp = $this->getClientIp($request);
        
        return new JsonResponse([
            'success' => true,
            'excluded' => $exclusion['excluded'],
            'reason' => $exclusion['reason'],
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'clientIp' => $clientIp,
            'anonymizedIp' => $exclusionService->anonymizeIp($clientIp),
        ]);
    }
    
    /**
     * Nettoyer les données analytics de l'utilisateur actuel
     */
    #[Route('/cleanup-my-data', name: 'app_analytics_cleanup_my_data', methods: ['POST'])]
    public function cleanupMyData(
        Request $request,
        EntityManagerInterface $em,
        AnalyticsExclusionService $exclusionService
    ): JsonResponse {
        $clientIp = $this->getClientIp($request);
        $anonymizedIp = $exclusionService->anonymizeIp($clientIp);
        
        $conn = $em->getConnection();
        $ipPattern = substr($anonymizedIp, 0, -1) . '%';
        
        // Compter et supprimer les analytics
        $countAnalytics = $conn->executeQuery(
            "SELECT COUNT(*) as total FROM analytics WHERE ip_address LIKE :ip_pattern",
            ['ip_pattern' => $ipPattern]
        )->fetchOne();
        
        $conn->executeStatement(
            "DELETE FROM analytics WHERE ip_address LIKE :ip_pattern",
            ['ip_pattern' => $ipPattern]
        );
        
        // Compter et supprimer les consentements cookies
        $countConsents = $conn->executeQuery(
            "SELECT COUNT(*) as total FROM cookie_consent WHERE ip_address LIKE :ip_pattern",
            ['ip_pattern' => $ipPattern]
        )->fetchOne();
        
        $conn->executeStatement(
            "DELETE FROM cookie_consent WHERE ip_address LIKE :ip_pattern",
            ['ip_pattern' => $ipPattern]
        );
        
        return new JsonResponse([
            'success' => true,
            'message' => "Nettoyage effectue",
            'deletedAnalytics' => (int) $countAnalytics,
            'deletedConsents' => (int) $countConsents,
            'totalDeleted' => (int) $countAnalytics + (int) $countConsents,
            'ip' => $anonymizedIp,
        ]);
    }
    
    /**
     * Nettoyer TOUTES les données liées au rôle ADMIN
     */
    #[Route('/cleanup-admin-data', name: 'app_analytics_cleanup_admin_data', methods: ['POST'])]
    public function cleanupAdminData(
        EntityManagerInterface $em
    ): JsonResponse {
        $conn = $em->getConnection();
        
        // Compter et supprimer les analytics des 7 derniers jours
        $countAnalytics = $conn->executeQuery(
            "SELECT COUNT(*) as total FROM analytics WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
        )->fetchOne();
        
        $conn->executeStatement(
            "DELETE FROM analytics WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
        );
        
        // Compter et supprimer les consentements des 7 derniers jours
        $countConsents = $conn->executeQuery(
            "SELECT COUNT(*) as total FROM cookie_consent WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
        )->fetchOne();
        
        $conn->executeStatement(
            "DELETE FROM cookie_consent WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
        );
        
        return new JsonResponse([
            'success' => true,
            'message' => "Donnees des 7 derniers jours supprimees",
            'deletedAnalytics' => (int) $countAnalytics,
            'deletedConsents' => (int) $countConsents,
            'totalDeleted' => (int) $countAnalytics + (int) $countConsents,
            'note' => "Cela inclut potentiellement vos sessions de test"
        ]);
    }
    
    /**
     * Statistiques sur les données potentiellement "admin"
     */
    #[Route('/admin-data-stats', name: 'app_analytics_admin_data_stats', methods: ['GET'])]
    public function getAdminDataStats(
        Request $request,
        EntityManagerInterface $em,
        AnalyticsExclusionService $exclusionService
    ): JsonResponse {
        $conn = $em->getConnection();
        $clientIp = $this->getClientIp($request);
        $anonymizedIp = $exclusionService->anonymizeIp($clientIp);
        $ipPattern = substr($anonymizedIp, 0, -1) . '%';
        
        // Compter les analytics avec l'IP actuelle
        $myAnalyticsCount = $conn->executeQuery(
            "SELECT COUNT(*) as total FROM analytics WHERE ip_address LIKE :ip_pattern",
            ['ip_pattern' => $ipPattern]
        )->fetchOne();
        
        // Compter les consentements avec l'IP actuelle
        $myConsentsCount = $conn->executeQuery(
            "SELECT COUNT(*) as total FROM cookie_consent WHERE ip_address LIKE :ip_pattern",
            ['ip_pattern' => $ipPattern]
        )->fetchOne();
        
        // Compter les entrées sur /admin
        $adminPathCount = $conn->executeQuery(
            "SELECT COUNT(*) as total FROM analytics WHERE page_url LIKE '/admin%'"
        )->fetchOne();
        
        // Compter les analytics des 7 derniers jours
        $recentAnalyticsCount = $conn->executeQuery(
            "SELECT COUNT(*) as total FROM analytics WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
        )->fetchOne();
        
        // Compter les consentements des 7 derniers jours
        $recentConsentsCount = $conn->executeQuery(
            "SELECT COUNT(*) as total FROM cookie_consent WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
        )->fetchOne();
        
        // Total des entrées
        $totalAnalytics = $conn->executeQuery("SELECT COUNT(*) as total FROM analytics")->fetchOne();
        $totalConsents = $conn->executeQuery("SELECT COUNT(*) as total FROM cookie_consent")->fetchOne();
        
        return new JsonResponse([
            'success' => true,
            'stats' => [
                'myIpAnalytics' => (int) $myAnalyticsCount,
                'myIpConsents' => (int) $myConsentsCount,
                'myIpTotal' => (int) $myAnalyticsCount + (int) $myConsentsCount,
                'adminPaths' => (int) $adminPathCount,
                'last7DaysAnalytics' => (int) $recentAnalyticsCount,
                'last7DaysConsents' => (int) $recentConsentsCount,
                'last7DaysTotal' => (int) $recentAnalyticsCount + (int) $recentConsentsCount,
                'totalAnalytics' => (int) $totalAnalytics,
                'totalConsents' => (int) $totalConsents,
                'totalAll' => (int) $totalAnalytics + (int) $totalConsents,
            ],
            'ip' => $anonymizedIp,
        ]);
    }
    
    /**
     * Récupère l'IP réelle du client
     */
    private function getClientIp(Request $request): ?string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
        ];

        foreach ($headers as $header) {
            $ip = $request->server->get($header);
            if ($ip) {
                if (str_contains($ip, ',')) {
                    $ips = array_map('trim', explode(',', $ip));
                    $ip = $ips[0];
                }
                return $ip;
            }
        }

        return $request->getClientIp();
    }
}

