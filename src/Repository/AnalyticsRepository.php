<?php

namespace App\Repository;

use App\Entity\Analytics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Analytics>
 */
class AnalyticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Analytics::class);
    }

    /**
     * Statistiques générales
     */
    public function getGeneralStats(?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a');
        
        // Filtrer par période si spécifié
        if ($days !== null) {
            $qb->where('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        $result = $qb->select(
            'COUNT(DISTINCT a.sessionId) as uniqueVisitors',
            'COUNT(a.id) as totalVisits',
            'AVG(a.timeOnPage) as avgTimeOnPage',
            'SUM(CASE WHEN a.isBounce = true THEN 1 ELSE 0 END) as bounces'
        )
        ->getQuery()
        ->getSingleResult();
        
        $bounceRate = $result['totalVisits'] > 0 
            ? round(($result['bounces'] / $result['totalVisits']) * 100, 1) 
            : 0;
        
        return [
            'uniqueVisitors' => (int) $result['uniqueVisitors'],
            'totalVisits' => (int) $result['totalVisits'],
            'avgTimeOnPage' => round($result['avgTimeOnPage'] ?? 0, 0),
            'bounceRate' => $bounceRate,
        ];
    }

    /**
     * Pages les plus visitées
     */
    public function getTopPages(int $limit = 10, ?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.pageUrl', 'a.pageTitle', 'COUNT(a.id) as visits')
            ->groupBy('a.pageUrl', 'a.pageTitle')
            ->orderBy('visits', 'DESC')
            ->setMaxResults($limit);
            
        if ($days !== null) {
            $qb->where('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Sources de trafic
     */
    public function getTrafficSources(int $limit = 20, ?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.referrer', 'COUNT(a.id) as visits')
            ->groupBy('a.referrer')
            ->orderBy('visits', 'DESC')
            ->setMaxResults($limit);
            
        if ($days !== null) {
            $qb->where('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Statistiques par appareil
     */
    public function getDeviceStats(?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.device', 'COUNT(a.id) as count')
            ->groupBy('a.device')
            ->orderBy('count', 'DESC');
            
        if ($days !== null) {
            $qb->where('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Statistiques par navigateur
     */
    public function getBrowserStats(?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.browser', 'COUNT(a.id) as count')
            ->groupBy('a.browser')
            ->orderBy('count', 'DESC');
            
        if ($days !== null) {
            $qb->where('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Visites par jour (30 derniers jours)
     * Utilisation de SQL natif car DATE() n'est pas supporté en DQL
     */
    public function getVisitsByDay(int $days = 30): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = '
            SELECT 
                DATE(visited_at) as date,
                COUNT(*) as visits,
                COUNT(DISTINCT session_id) as uniqueVisitors
            FROM analytics
            WHERE visited_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
            GROUP BY DATE(visited_at)
            ORDER BY date ASC
        ';
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['days' => $days]);
        
        return $result->fetchAllAssociative();
    }

    /**
     * Visites par heure de la journée
     */
    public function getVisitsByHour(?int $days = null): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = '
            SELECT 
                HOUR(visited_at) as hour,
                COUNT(*) as visits
            FROM analytics
        ';
        
        if ($days !== null) {
            $sql .= ' WHERE visited_at >= DATE_SUB(NOW(), INTERVAL :days DAY)';
        }
        
        $sql .= '
            GROUP BY HOUR(visited_at)
            ORDER BY hour ASC
        ';
        
        $stmt = $conn->prepare($sql);
        $params = $days !== null ? ['days' => $days] : [];
        $result = $stmt->executeQuery($params);
        
        return $result->fetchAllAssociative();
    }

    /**
     * Top pays
     */
    public function getTopCountries(int $limit = 10, ?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.country', 'COUNT(a.id) as visits')
            ->where('a.country IS NOT NULL')
            ->groupBy('a.country')
            ->orderBy('visits', 'DESC')
            ->setMaxResults($limit);
            
        if ($days !== null) {
            $qb->andWhere('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Top campagnes UTM
     */
    public function getTopCampaigns(int $limit = 10, ?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.utmCampaign', 'a.utmSource', 'a.utmMedium', 'COUNT(a.id) as visits')
            ->where('a.utmCampaign IS NOT NULL')
            ->groupBy('a.utmCampaign', 'a.utmSource', 'a.utmMedium')
            ->orderBy('visits', 'DESC')
            ->setMaxResults($limit);
            
        if ($days !== null) {
            $qb->andWhere('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouver une visite par session et page
     */
    public function findBySessionAndPage(string $sessionId, string $pageUrl): ?Analytics
    {
        return $this->createQueryBuilder('a')
            ->where('a.sessionId = :sessionId')
            ->andWhere('a.pageUrl = :pageUrl')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('pageUrl', $pageUrl)
            ->orderBy('a.visitedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * ===== NOUVEAUX VS RETOURS =====
     */

    /**
     * Nouveaux visiteurs vs Retours
     */
    public function getNewVsReturningVisitors(?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select(
                'SUM(CASE WHEN a.isNewVisitor = true THEN 1 ELSE 0 END) as newVisitors',
                'SUM(CASE WHEN a.isNewVisitor = false THEN 1 ELSE 0 END) as returningVisitors'
            );
            
        if ($days !== null) {
            $qb->where('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        $result = $qb->getQuery()->getSingleResult();
        
        return [
            'new' => (int) $result['newVisitors'],
            'returning' => (int) $result['returningVisitors'],
        ];
    }

    /**
     * Taux de rétention
     */
    public function getRetentionRate(?int $days = null): float
    {
        $data = $this->getNewVsReturningVisitors($days);
        $total = $data['new'] + $data['returning'];
        
        if ($total === 0) {
            return 0.0;
        }
        
        return round(($data['returning'] / $total) * 100, 1);
    }

    /**
     * ===== ENGAGEMENT =====
     */

    /**
     * Taux d'engagement
     */
    public function getEngagementRate(?int $days = null): float
    {
        $qb = $this->createQueryBuilder('a');
        
        if ($days !== null) {
            $qb->where('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        $result = $qb->select(
            'COUNT(a.id) as total',
            'SUM(CASE WHEN a.isEngaged = true THEN 1 ELSE 0 END) as engaged'
        )
        ->getQuery()
        ->getSingleResult();
        
        $total = (int) $result['total'];
        $engaged = (int) $result['engaged'];
        
        if ($total === 0) {
            return 0.0;
        }
        
        return round(($engaged / $total) * 100, 1);
    }

    /**
     * Pages moyennes par session
     */
    public function getAveragePagesPerSession(?int $days = null): float
    {
        $qb = $this->createQueryBuilder('a')
            ->select('AVG(a.pagesPerSession) as avgPages');
            
        if ($days !== null) {
            $qb->where('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        $result = $qb->getQuery()->getSingleResult();
        
        return round($result['avgPages'] ?? 0, 1);
    }

    /**
     * ===== PARCOURS UTILISATEUR =====
     */

    /**
     * Top pages de sortie
     */
    public function getTopExitPages(int $limit = 10, ?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.exitPage', 'COUNT(a.id) as exits')
            ->where('a.exitPage IS NOT NULL')
            ->groupBy('a.exitPage')
            ->orderBy('exits', 'DESC')
            ->setMaxResults($limit);
            
        if ($days !== null) {
            $qb->andWhere('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Top pages d'entrée (landing pages)
     */
    public function getTopLandingPages(int $limit = 10, ?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.landingPage', 'COUNT(a.id) as entries')
            ->where('a.landingPage IS NOT NULL')
            ->groupBy('a.landingPage')
            ->orderBy('entries', 'DESC')
            ->setMaxResults($limit);
            
        if ($days !== null) {
            $qb->andWhere('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Parcours les plus fréquents (page précédente → page actuelle)
     */
    public function getTopUserFlows(int $limit = 20, ?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.previousPageUrl', 'a.pageUrl', 'COUNT(a.id) as count')
            ->where('a.previousPageUrl IS NOT NULL')
            ->groupBy('a.previousPageUrl', 'a.pageUrl')
            ->orderBy('count', 'DESC')
            ->setMaxResults($limit);
            
        if ($days !== null) {
            $qb->andWhere('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * ===== RECHERCHE INTERNE =====
     */

    /**
     * Recherches les plus fréquentes
     */
    public function getTopSearchQueries(int $limit = 20, ?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.searchQuery', 'COUNT(a.id) as searches', 'AVG(a.searchResultsCount) as avgResults')
            ->where('a.searchQuery IS NOT NULL')
            ->groupBy('a.searchQuery')
            ->orderBy('searches', 'DESC')
            ->setMaxResults($limit);
            
        if ($days !== null) {
            $qb->andWhere('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Recherches sans résultats (opportunité de contenu)
     */
    public function getSearchesWithNoResults(int $limit = 20, ?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.searchQuery', 'COUNT(a.id) as searches')
            ->where('a.searchQuery IS NOT NULL')
            ->andWhere('a.searchResultsCount = 0')
            ->groupBy('a.searchQuery')
            ->orderBy('searches', 'DESC')
            ->setMaxResults($limit);
            
        if ($days !== null) {
            $qb->andWhere('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * ===== PERFORMANCE WEB =====
     */

    /**
     * Performance moyenne des pages
     */
    public function getAveragePerformance(?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a');
        
        if ($days !== null) {
            $qb->where('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        $result = $qb->select(
            'AVG(a.pageLoadTime) as avgLoadTime',
            'AVG(a.domReadyTime) as avgDomReady',
            'AVG(a.firstPaintTime) as avgFirstPaint'
        )
        ->getQuery()
        ->getSingleResult();
        
        return [
            'avgLoadTime' => round($result['avgLoadTime'] ?? 0, 0),
            'avgDomReady' => round($result['avgDomReady'] ?? 0, 0),
            'avgFirstPaint' => round($result['avgFirstPaint'] ?? 0, 0),
        ];
    }

    /**
     * Pages les plus lentes
     */
    public function getSlowestPages(int $limit = 10, ?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.pageUrl', 'AVG(a.pageLoadTime) as avgLoadTime', 'COUNT(a.id) as visits')
            ->where('a.pageLoadTime IS NOT NULL')
            ->groupBy('a.pageUrl')
            ->orderBy('avgLoadTime', 'DESC')
            ->setMaxResults($limit);
            
        if ($days !== null) {
            $qb->andWhere('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * ===== ANALYSE AVANCÉE =====
     */

    /**
     * Distribution du temps passé sur le site
     */
    public function getTimeOnSiteDistribution(?int $days = null): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = '
            SELECT 
                CASE
                    WHEN time_on_page < 10 THEN "0-10s"
                    WHEN time_on_page < 30 THEN "10-30s"
                    WHEN time_on_page < 60 THEN "30-60s"
                    WHEN time_on_page < 180 THEN "1-3min"
                    WHEN time_on_page < 600 THEN "3-10min"
                    ELSE "10min+"
                END as timeRange,
                COUNT(*) as count
            FROM analytics
        ';
        
        if ($days !== null) {
            $sql .= ' WHERE visited_at >= DATE_SUB(NOW(), INTERVAL :days DAY)';
        }
        
        $sql .= ' GROUP BY timeRange ORDER BY 
            CASE timeRange
                WHEN "0-10s" THEN 1
                WHEN "10-30s" THEN 2
                WHEN "30-60s" THEN 3
                WHEN "1-3min" THEN 4
                WHEN "3-10min" THEN 5
                WHEN "10min+" THEN 6
            END';
        
        $stmt = $conn->prepare($sql);
        $params = $days !== null ? ['days' => $days] : [];
        $result = $stmt->executeQuery($params);
        
        return $result->fetchAllAssociative();
    }

    /**
     * Distribution du scroll depth
     */
    public function getScrollDepthDistribution(?int $days = null): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = '
            SELECT 
                CASE
                    WHEN scroll_depth < 25 THEN "0-25%"
                    WHEN scroll_depth < 50 THEN "25-50%"
                    WHEN scroll_depth < 75 THEN "50-75%"
                    WHEN scroll_depth < 90 THEN "75-90%"
                    ELSE "90-100%"
                END as scrollRange,
                COUNT(*) as count
            FROM analytics
            WHERE scroll_depth IS NOT NULL
        ';
        
        if ($days !== null) {
            $sql .= ' AND visited_at >= DATE_SUB(NOW(), INTERVAL :days DAY)';
        }
        
        $sql .= ' GROUP BY scrollRange ORDER BY 
            CASE scrollRange
                WHEN "0-25%" THEN 1
                WHEN "25-50%" THEN 2
                WHEN "50-75%" THEN 3
                WHEN "75-90%" THEN 4
                WHEN "90-100%" THEN 5
            END';
        
        $stmt = $conn->prepare($sql);
        $params = $days !== null ? ['days' => $days] : [];
        $result = $stmt->executeQuery($params);
        
        return $result->fetchAllAssociative();
    }

    /**
     * Sessions par utilisateur (combien de fois ils reviennent)
     */
    public function getSessionFrequency(?int $days = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.sessionCount', 'COUNT(DISTINCT a.sessionId) as users')
            ->where('a.sessionCount IS NOT NULL')
            ->groupBy('a.sessionCount')
            ->orderBy('a.sessionCount', 'ASC');
            
        if ($days !== null) {
            $qb->andWhere('a.visitedAt >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }
        
        return $qb->getQuery()->getResult();
    }
}
