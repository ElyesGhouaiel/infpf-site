<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 *
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    // src/Repository/FormationRepository.php
    public function findByCriteria(array $criteria)
    {
        $queryBuilder = $this->createQueryBuilder('f');
    
        // Filtre par thématique
        if (!empty($criteria['thematique'])) {
            $queryBuilder->andWhere('f.thematique LIKE :thematique')
                ->setParameter('thematique', '%' . $criteria['thematique'] . '%');
        }
    
        // Filtre par format
        if (!empty($criteria['format'])) {
            $queryBuilder->andWhere('f.format = :format')
                ->setParameter('format', $criteria['format']);
        }
    
        // Filtre par durée
        if (!empty($criteria['duree'])) {
            $queryBuilder->andWhere('f.dureeFormation = :duree')
                ->setParameter('duree', $criteria['duree']);
        }
    
        // Filtre par niveau
        if (!empty($criteria['niveau'])) {
            $queryBuilder->andWhere('f.niveau = :niveau')
                ->setParameter('niveau', $criteria['niveau']);
        }
    
        // Filtre par langue
        if (!empty($criteria['langue'])) {
            $queryBuilder->andWhere('f.langue = :langue')
                ->setParameter('langue', $criteria['langue']);
        }
    
        // Filtre par financement
        if (!empty($criteria['financement'])) {
            $queryBuilder->andWhere('f.financement = :financement')
                ->setParameter('financement', $criteria['financement']);
        }
    
        return $queryBuilder->getQuery()->getResult();
    }

// public function findBySearchAndSort(string $searchTerm = '', string $sortOrder = 'asc', ?string $categoryId = null)
// {
//     $qb = $this->createQueryBuilder('f')
//                ->leftJoin('f.category', 'c');

//     if (!empty($searchTerm)) {
//         $qb->andWhere('f.name LIKE :searchTerm')
//            ->setParameter('searchTerm', '%' . $searchTerm . '%');
//     }

//     if ($sortOrder === 'desc') {
//         $qb->orderBy('f.priceFormation', 'DESC');
//     } else {
//         $qb->orderBy('f.priceFormation', 'ASC');
//     }

//     if (!empty($categoryId)) {
//         $qb->andWhere('c.id = :categoryId')
//            ->setParameter('categoryId', $categoryId);
//     }

//     return $qb->getQuery()->getResult();
// }
public function findBySortOrder(string $sortOrder = 'asc', ?string $categoryId = null)
{
    $qb = $this->createQueryBuilder('f')
               ->leftJoin('f.category', 'c');

    if (!empty($categoryId)) {
        $qb->where('c.id = :categoryId')
           ->setParameter('categoryId', $categoryId);
    }

    if ($sortOrder === 'desc') {
        $qb->orderBy('f.priceFormation', 'DESC');
    } else {
        $qb->orderBy('f.priceFormation', 'ASC');
    }

    return $qb->getQuery()->getResult();
}


public function findBySearchTerm(string $searchTerm = '', string $sortField = 'nameFormation', string $sortOrder = 'ASC')
{
    $qb = $this->createQueryBuilder('f')
               ->leftJoin('f.category', 'c');

    if (!empty($searchTerm)) {
        $qb->where('f.nameFormation LIKE :searchTerm OR f.descriptionFormation LIKE :searchTerm')
           ->setParameter('searchTerm', '%' . $searchTerm . '%');
    }

    // Détermine le champ de tri en fonction de $sortField
    switch ($sortField) {
        case 'priceFormation':
            $qb->orderBy('f.' . $sortField, $sortOrder);
            break;
        case 'nameFormation':
        default:
            $qb->orderBy('f.nameFormation', $sortOrder);
            break;
    }

    return $qb->getQuery()->getResult();
}



//    /**
//     * @return Formation[] Returns an array of Formation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Formation
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


}