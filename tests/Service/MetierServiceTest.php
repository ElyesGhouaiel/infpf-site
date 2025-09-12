<?php

namespace App\Tests\Service;

use App\Service\MetierService;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MetierServiceTest extends KernelTestCase
{
    private MetierService $metierService;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->metierService = static::getContainer()->get(MetierService::class);
    }

    public function testGetMetiersThematiques(): void
    {
        $thematiques = $this->metierService->getMetiersThematiques();
        
        $this->assertIsArray($thematiques);
        $this->assertNotEmpty($thematiques, 'Les thématiques métiers doivent être configurées');
        
        // Vérifier la structure d'une thématique
        foreach ($thematiques as $slug => $thematique) {
            $this->assertArrayHasKey('slug', $thematique);
            $this->assertArrayHasKey('title_fr', $thematique);
            $this->assertArrayHasKey('title_en', $thematique);
            $this->assertArrayHasKey('keywords', $thematique);
            $this->assertIsArray($thematique['keywords']);
        }
    }

    public function testGetMetierBySlugValid(): void
    {
        $metier = $this->metierService->getMetierBySlug('vente-commerce');
        
        $this->assertIsArray($metier);
        $this->assertEquals('vente-commerce', $metier['slug']);
        $this->assertArrayHasKey('title_fr', $metier);
        $this->assertArrayHasKey('keywords', $metier);
    }

    public function testGetMetierBySlugInvalid(): void
    {
        $metier = $this->metierService->getMetierBySlug('slug-inexistant');
        
        $this->assertNull($metier);
    }

    public function testSlugify(): void
    {
        $slug = $this->metierService->slugify('Développement Web & WordPress');
        
        $this->assertIsString($slug);
        $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $slug);
        $this->assertEquals('developpement-web-wordpress', $slug);
    }

    public function testIsMetiersEnabled(): void
    {
        $enabled = $this->metierService->isMetiersEnabled();
        
        $this->assertIsBool($enabled);
        // Par défaut, la fonctionnalité devrait être activée
        $this->assertTrue($enabled);
    }

    public function testFindFormationsByMetierStructure(): void
    {
        // Test de la structure de retour, même si vide
        $formations = $this->metierService->findFormationsByMetier('vente-commerce');
        
        $this->assertIsArray($formations);
        
        // Si des formations sont trouvées, vérifier qu'elles sont des instances Formation
        foreach ($formations as $formation) {
            $this->assertInstanceOf(\App\Entity\Formation::class, $formation);
        }
    }

    public function testGenerateJsonLdStructure(): void
    {
        $formations = []; // Test avec formations vides
        $jsonLd = $this->metierService->generateJsonLd('vente-commerce', $formations, 'fr');
        
        $this->assertIsArray($jsonLd);
        
        if (!empty($jsonLd)) {
            $this->assertArrayHasKey('@context', $jsonLd);
            $this->assertArrayHasKey('@type', $jsonLd);
            $this->assertEquals('https://schema.org', $jsonLd['@context']);
            $this->assertEquals('ItemList', $jsonLd['@type']);
            $this->assertArrayHasKey('numberOfItems', $jsonLd);
            $this->assertEquals(0, $jsonLd['numberOfItems']);
        }
    }

    public function testGetFormationsCountByThematiqueStructure(): void
    {
        $counts = $this->metierService->getFormationsCountByThematique();
        
        $this->assertIsArray($counts);
        
        // Vérifier que chaque thématique a un count
        $thematiques = $this->metierService->getMetiersThematiques();
        foreach ($thematiques as $slug => $thematique) {
            $this->assertArrayHasKey($slug, $counts);
            $this->assertIsInt($counts[$slug]);
            $this->assertGreaterThanOrEqual(0, $counts[$slug]);
        }
    }

    public function testConfigurationFileExists(): void
    {
        $kernel = static::getContainer()->get('kernel');
        $configPath = $kernel->getProjectDir() . '/config/metiers.yaml';
        
        $this->assertFileExists($configPath, 'Le fichier de configuration metiers.yaml doit exister');
        $this->assertFileIsReadable($configPath, 'Le fichier de configuration doit être lisible');
    }
}
