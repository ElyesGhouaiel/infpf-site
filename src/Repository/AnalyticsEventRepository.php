<?php

namespace App\Repository;

use App\Entity\AnalyticsEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnalyticsEvent>
 */
class AnalyticsEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalyticsEvent::class);
    }

    /**
     * Top événements
     */
    public function getTopEvents(int $limit = 10, ?int $days = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.eventName', 'COUNT(e.id) as count')
            ->groupBy('e.eventName')
            ->orderBy('count', 'DESC')
            ->setMaxResults($limit);

        if ($days !== null) {
            $qb->where('e.eventDate >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Événements par catégorie
     */
    public function getEventsByCategory(?int $days = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.eventCategory', 'COUNT(e.id) as count')
            ->groupBy('e.eventCategory')
            ->orderBy('count', 'DESC');

        if ($days !== null) {
            $qb->where('e.eventDate >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Conversions (événements de type conversion)
     */
    public function getConversions(?int $days = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.eventName', 'COUNT(e.id) as count', 'SUM(e.eventValue) as totalValue')
            ->where('e.eventCategory = :category')
            ->setParameter('category', 'conversion')
            ->groupBy('e.eventName')
            ->orderBy('count', 'DESC');

        if ($days !== null) {
            $qb->andWhere('e.eventDate >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Total des événements
     */
    public function getTotalEvents(?int $days = null): int
    {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)');

        if ($days !== null) {
            $qb->where('e.eventDate >= :startDate')
               ->setParameter('startDate', new \DateTime("-{$days} days"));
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}












