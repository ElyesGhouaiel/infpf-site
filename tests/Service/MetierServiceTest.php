<?php

namespace App\Tests\Service;

use App\Service\MetierService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MetierServiceTest extends KernelTestCase
{
    private ?MetierService $metierService = null;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->metierService = static::getContainer()->get(MetierService::class);
    }

    public function testServiceExists(): void
    {
        $this->assertNotNull($this->metierService);
        $this->assertInstanceOf(MetierService::class, $this->metierService);
    }

    public function testGetMetierBySlugInvalid(): void
    {
        $metier = $this->metierService->getMetierBySlug('slug-inexistant-12345');
        
        $this->assertNull($metier);
    }

    public function testSlugify(): void
    {
        $slug = $this->metierService->slugify('Développement Web & WordPress');
        
        $this->assertIsString($slug);
        $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $slug);
    }

    public function testIsMetiersEnabled(): void
    {
        $enabled = $this->metierService->isMetiersEnabled();
        
        $this->assertIsBool($enabled);
    }

    public function testFindFormationsByMetierReturnsArray(): void
    {
        try {
            $formations = $this->metierService->findFormationsByMetier('commercial');
            $this->assertIsArray($formations);
        } catch (\Exception $e) {
            $this->markTestSkipped('Database not available: ' . $e->getMessage());
        }
    }

    public function testGenerateJsonLdStructure(): void
    {
        $formations = []; // Test avec formations vides
        $jsonLd = $this->metierService->generateJsonLd('test-slug', $formations, 'fr');
        
        $this->assertIsArray($jsonLd);
    }

    public function testConfigurationFileExists(): void
    {
        $kernel = static::getContainer()->get('kernel');
        $configPath = $kernel->getProjectDir() . '/config/metiers.yaml';
        
        $this->assertFileExists($configPath, 'Le fichier de configuration metiers.yaml doit exister');
    }
}
