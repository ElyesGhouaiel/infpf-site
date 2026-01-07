<?php

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
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.category', 'c')
            ->addSelect('c');

        if (!empty($criteria['thematique'])) {
            $qb->andWhere('IDENTITY(f.category) IN (:thematique)')
               ->setParameter('thematique', $criteria['thematique']);
        }

        if (!empty($criteria['lieu'])) {
            $qb->andWhere('f.lieu IN (:lieu)')
               ->setParameter('lieu', $criteria['lieu']);
        }

        // Le filtrage par durée sera fait côté PHP dans le Controller
        // car les formats de durée sont trop variés pour SQL

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
    
    /**
     * Extrait le premier nombre d'une chaîne de durée.
     * Ex: "20 à 30 heures" → 20, "136 heures + 136 heures" → 136, "2 ans" → 0
     *
     * @param string|null $duration
     * @return int
     */
    public function extractHoursFromDuration(?string $duration): int
    {
        if (empty($duration)) {
            return 0;
        }
        
        // Extraire le premier nombre de la chaîne
        preg_match('/(\d+)/', $duration, $matches);
        
        if (!empty($matches[1])) {
            $hours = (int)$matches[1];
            
            // Si c'est en "ans", convertir en heures approximatives (1 an ≈ 400h)
            if (stripos($duration, 'an') !== false) {
                return $hours * 400;
            }
            
            return $hours;
        }
        
        return 0;
    }
    
    /**
     * Filtre les formations par durée (logique PHP).
     *
     * @param array $formations
     * @param array $durationRanges
     * @return array
     */
    public function filterByDuration(array $formations, array $durationRanges): array
    {
        if (empty($durationRanges)) {
            return $formations;
        }
        
        $filtered = [];
        
        foreach ($formations as $formation) {
            $hours = $this->extractHoursFromDuration($formation->getDureeFormation());
            
            foreach ($durationRanges as $range) {
                $match = false;
                
                switch ($range) {
                    case 'less_than_24':
                        $match = ($hours > 0 && $hours < 24);
                        break;
                    
                    case '24_to_100':
                        $match = ($hours >= 24 && $hours <= 100);
                        break;
                    
                    case '100_to_200':
                        $match = ($hours > 100 && $hours <= 200);
                        break;
                    
                    case 'more_than_200':
                        $match = ($hours > 200);
                        break;
                }
                
                if ($match) {
                    $filtered[] = $formation;
                    break; // Une formation ne doit être ajoutée qu'une seule fois
                }
            }
        }
        
        return $filtered;
    }

    /**
     * Compte les formations uniques par nom (sans doublons distanciel/présentiel).
     * Enlève les suffixes " - Distanciel" et " - Présentiel" du nom avant de compter.
     *
     * @return int
     */
    public function countUniqueByName(): int
    {
        // Récupérer tous les noms de formations
        $formations = $this->createQueryBuilder('f')
            ->select('f.nameFormation')
            ->getQuery()
            ->getResult();
        
        $uniqueNames = [];
        
        foreach ($formations as $formation) {
            $name = $formation['nameFormation'];
            
            // Enlever les suffixes " - Distanciel" et " - Présentiel"
            $baseName = preg_replace('/\s*-\s*(Distanciel|Présentiel|Presentiel)\s*$/i', '', $name);
            $baseName = trim($baseName);
            
            // Ajouter au tableau des noms uniques
            $uniqueNames[$baseName] = true;
        }
        
        return count($uniqueNames);
    }
}
