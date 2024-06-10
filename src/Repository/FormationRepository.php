<?php

// src/Repository/FormationRepository.php

// src/Repository/FormationRepository.php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    /**
     * Retourne les formations filtrées par différents critères.
     *
     * @param array $criteria
     * @return QueryBuilder
     */
    public function findFormationsByCriteria(array $criteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder('f');

        if (!empty($criteria['thematique'])) {
            $qb->andWhere('f.category IN (:thematique)')
               ->setParameter('thematique', $criteria['thematique']);
        }

        if (!empty($criteria['lieu'])) {
            $qb->andWhere('f.lieu IN (:lieu)')
               ->setParameter('lieu', $criteria['lieu']);
        }

        if (!empty($criteria['duration'])) {
            $qb->andWhere('f.dureeFormation IN (:duration)')
               ->setParameter('duration', $criteria['duration']);
        }

        if (!empty($criteria['level'])) {
            $qb->andWhere('f.niveau IN (:level)')
               ->setParameter('level', $criteria['level']);
        }

        if (!empty($criteria['language'])) {
            $qb->andWhere('f.langue IN (:language)')
               ->setParameter('language', $criteria['language']);
        }

        if (!empty($criteria['funding'])) {
            $qb->andWhere('f.funding = :funding')
               ->setParameter('funding', $criteria['funding']);
        }

        return $qb->orderBy('f.id', 'DESC');
    }
}