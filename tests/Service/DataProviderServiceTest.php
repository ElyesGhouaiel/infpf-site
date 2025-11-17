<?php

namespace App\Tests\Service;

use App\Service\DataProviderService;
use App\Repository\CategoryRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests unitaires pour DataProviderService
 */
class DataProviderServiceTest extends KernelTestCase
{
    private ?DataProviderService $dataProviderService = null;
    private ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $categoryRepository = $container->get(CategoryRepository::class);
        $formationRepository = $container->get(FormationRepository::class);
        
        $this->dataProviderService = new DataProviderService(
            $this->entityManager,
            $categoryRepository,
            $formationRepository
        );
    }

    public function testGetCategoriesReturnsArray(): void
    {
        $categories = $this->dataProviderService->getCategories();
        
        $this->assertIsArray($categories);
    }

    public function testGetFormationsReturnsArray(): void
    {
        $formations = $this->dataProviderService->getFormations();
        
        $this->assertIsArray($formations);
    }

    public function testGetCategoryByNameReturnsCategoryOrNull(): void
    {
        // Test avec un nom existant (si la catégorie existe)
        $category = $this->dataProviderService->getCategoryByName('IA');
        
        // Peut être null si la catégorie n'existe pas
        $this->assertTrue($category === null || is_object($category));
    }

    public function testGetTotalFormationsInCategoryReturnsInteger(): void
    {
        // Test avec un ID de catégorie (peut être 1 ou tout autre ID valide)
        $total = $this->dataProviderService->getTotalFormationsInCategory(1);
        
        $this->assertIsInt($total);
        $this->assertGreaterThanOrEqual(0, $total);
    }
}

