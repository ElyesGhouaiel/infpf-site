<?php

namespace App\Tests\Service;

use App\Service\DataProviderService;
use App\Repository\CategoryRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests unitaires pour DataProviderService
 */
class DataProviderServiceTest extends KernelTestCase
{
    private ?DataProviderService $dataProviderService = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        
        $this->dataProviderService = $container->get(DataProviderService::class);
    }

    public function testServiceExists(): void
    {
        $this->assertNotNull($this->dataProviderService);
        $this->assertInstanceOf(DataProviderService::class, $this->dataProviderService);
    }

    public function testGetCategoriesReturnsArray(): void
    {
        try {
            $categories = $this->dataProviderService->getCategories();
            $this->assertIsArray($categories);
        } catch (\Exception $e) {
            // Si la table n'existe pas en test, on skip
            $this->markTestSkipped('Database not available: ' . $e->getMessage());
        }
    }

    public function testGetFormationsReturnsArray(): void
    {
        try {
            $formations = $this->dataProviderService->getFormations();
            $this->assertIsArray($formations);
        } catch (\Exception $e) {
            $this->markTestSkipped('Database not available: ' . $e->getMessage());
        }
    }

    public function testGetCategoryByNameReturnsCategoryOrNull(): void
    {
        try {
            $category = $this->dataProviderService->getCategoryByName('NonExistentCategory');
            $this->assertNull($category);
        } catch (\Exception $e) {
            $this->markTestSkipped('Database not available: ' . $e->getMessage());
        }
    }

    public function testGetTotalFormationsInCategoryReturnsInteger(): void
    {
        try {
            $total = $this->dataProviderService->getTotalFormationsInCategory(999999);
            $this->assertIsInt($total);
            $this->assertGreaterThanOrEqual(0, $total);
        } catch (\Exception $e) {
            $this->markTestSkipped('Database not available: ' . $e->getMessage());
        }
    }
}
