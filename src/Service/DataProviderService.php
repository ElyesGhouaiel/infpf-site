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
        // Récupère toutes les formations
        return $this->formationRepository->findAll();
    }

    // Récupère les formations d'une catégorie spécifique
    public function getFormationsByCategory(Category $category)
    {
        return $this->formationRepository->findBy(['category' => $category]);
    }

    // Récupère une catégorie par son nom
    // Utile si vous avez besoin de trouver une catégorie spécifique sans connaître son ID
    public function getCategoryByName(string $name)
    {
        return $this->categoryRepository->findOneBy(['name' => $name]);
    }

    // Récupère les catégories qui ont au moins une formation
    // Utile pour filtrer et n'afficher que les catégories actives
    public function getCategoriesWithFormations()
    {
        return $this->categoryRepository->createQueryBuilder('c')
            ->innerJoin('c.formations', 'f')
            ->groupBy('c.id')
            ->having('COUNT(f.id) > 0')
            ->getQuery()
            ->getResult();
    }

    // Récupère le nombre de formations dans une catégorie spécifique
    // Utile pour afficher des statistiques ou pour des vérifications rapides
    public function countFormationsByCategory(Category $category)
    {
        return $this->formationRepository->count(['category' => $category]);
    }
    
    // Ajoutez ici d'autres méthodes selon les besoins spécifiques de votre application
    // Récupère le nombre total de formations dans une catégorie spécifique
    public function getTotalFormationsInCategory($categoryId)
    {
        $query = $this->entityManager->createQuery(
            'SELECT COUNT(f)
            FROM App\Entity\Formation f
            WHERE f.category = :categoryId'
        )->setParameter('categoryId', $categoryId);

        return $query->getSingleScalarResult();
    }
}