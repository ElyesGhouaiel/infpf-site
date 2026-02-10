<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use App\Repository\FormationRepository;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Formation;
// use App\Service\DataProviderServiceController;

class DataProviderService
{
    private $entityManager;
    private $categoryRepository;
    private $formationRepository;


    public function __construct(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, FormationRepository $formationRepository)
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
        $this->formationRepository = $formationRepository;
    }

    public function getCategories()
    {
        // Récupère toutes les catégories
        return $this->categoryRepository->findAll();
    }

    public function getFormations()
    {
        // Récupère toutes les formations actives (visibles sur le site)
        return $this->formationRepository->findAllActive();
    }

    // Récupère les formations actives d'une catégorie spécifique
    public function getFormationsByCategory(Category $category)
    {
        return $this->formationRepository->findActiveBy(['category' => $category]);
    }

    // Récupère une catégorie par son nom
    // Utile si vous avez besoin de trouver une catégorie spécifique sans connaître son ID
    public function getCategoryByName(string $name)
    {
        return $this->categoryRepository->findOneBy(['name' => $name]);
    }

    // Récupère les catégories qui ont au moins une formation active
    // Utile pour filtrer et n'afficher que les catégories actives
    public function getCategoriesWithFormations()
    {
        return $this->categoryRepository->createQueryBuilder('c')
            ->innerJoin('c.formations', 'f')
            ->andWhere('f.isActive = :active')
            ->setParameter('active', true)
            ->groupBy('c.id')
            ->having('COUNT(f.id) > 0')
            ->getQuery()
            ->getResult();
    }

    // Récupère le nombre de formations actives dans une catégorie spécifique
    public function countFormationsByCategory(Category $category)
    {
        return $this->formationRepository->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->andWhere('f.category = :category')
            ->andWhere('f.isActive = :active')
            ->setParameter('category', $category)
            ->setParameter('active', true)
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    // Récupère le nombre total de formations actives dans une catégorie spécifique
    public function getTotalFormationsInCategory($categoryId)
    {
        $query = $this->entityManager->createQuery(
            'SELECT COUNT(f)
            FROM App\Entity\Formation f
            WHERE f.category = :categoryId
            AND f.isActive = :active'
        )->setParameter('categoryId', $categoryId)
         ->setParameter('active', true);

        return $query->getSingleScalarResult();
    }
}