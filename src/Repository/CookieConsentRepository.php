<?php

namespace App\Repository;

use App\Entity\CookieConsent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CookieConsent>
 */
class CookieConsentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CookieConsent::class);
    }

    /**
     * Trouver un consentement par token
     */
    public function findByToken(string $token): ?CookieConsent
    {
        return $this->createQueryBuilder('c')
            ->where('c.consentToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Statistiques de consentement
     */
    public function countByType(): array
    {
        $qb = $this->createQueryBuilder('c');
        
        $result = $qb->select(
            'COUNT(c.id) as total',
            'SUM(CASE WHEN c.analyticsCookies = true THEN 1 ELSE 0 END) as analytics',
            'SUM(CASE WHEN c.marketingCookies = true THEN 1 ELSE 0 END) as marketing'
        )
        ->getQuery()
        ->getSingleResult();
        
        return [
            'total' => (int) $result['total'],
            'analytics' => (int) $result['analytics'],
            'marketing' => (int) $result['marketing'],
        ];
    }

    /**
     * Consentements par jour (30 derniers jours)
     */
    public function getConsentStatsByDay(int $days = 30): array
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = '
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as total,
                SUM(CASE WHEN analytics_cookies = 1 THEN 1 ELSE 0 END) as analytics,
                SUM(CASE WHEN marketing_cookies = 1 THEN 1 ELSE 0 END) as marketing
            FROM cookie_consent
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ';
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['days' => $days]);
        
        return $result->fetchAllAssociative();
    }

    /**
     * Taux d'acceptation global
     */
    public function getAcceptanceRate(): array
    {
        $total = $this->count([]);
        
        if ($total === 0) {
            return [
                'analyticsRate' => 0,
                'marketingRate' => 0,
            ];
        }
        
        $analyticsAccepted = $this->count(['analyticsCookies' => true]);
        $marketingAccepted = $this->count(['marketingCookies' => true]);
        
        return [
            'analyticsRate' => round(($analyticsAccepted / $total) * 100, 1),
            'marketingRate' => round(($marketingAccepted / $total) * 100, 1),
        ];
    }
}
