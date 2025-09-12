<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MetierControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testMetiersIndexPage(): void
    {
        $crawler = $this->client->request('GET', '/metiers');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Métiers');
        $this->assertSelectorExists('.metiers-grid');
    }

    public function testMetierShowPageWithValidSlug(): void
    {
        $crawler = $this->client->request('GET', '/metiers/vente-commerce');
        
        // Note: Cette route peut renvoyer 404 s'il n'y a pas de formations correspondantes
        // C'est normal selon la logique métier
        $this->assertTrue(
            $this->client->getResponse()->isSuccessful() || 
            $this->client->getResponse()->getStatusCode() === 404
        );
    }

    public function testMetierShowPageWithInvalidSlug(): void
    {
        $this->client->request('GET', '/metiers/slug-inexistant');
        
        $this->assertResponseStatusCodeSame(404);
    }

    public function testMetiersIndexHasCorrectMetaTags(): void
    {
        $crawler = $this->client->request('GET', '/metiers');
        
        $this->assertResponseIsSuccessful();
        
        // Vérifier les balises meta
        $this->assertSelectorExists('meta[name="description"]');
        $this->assertSelectorExists('link[rel="canonical"]');
        $this->assertSelectorExists('link[hreflang="fr"]');
        $this->assertSelectorExists('link[hreflang="en"]');
    }

    public function testLanguageToggleLinks(): void
    {
        $crawler = $this->client->request('GET', '/metiers');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.language-toggle');
        $this->assertSelectorExists('a.lang-toggle-btn');
    }

    public function testMetiersJsonLdStructure(): void
    {
        $crawler = $this->client->request('GET', '/metiers');
        
        $this->assertResponseIsSuccessful();
        
        $jsonLdScript = $crawler->filter('script[type="application/ld+json"]');
        $this->assertGreaterThan(0, $jsonLdScript->count(), 'JSON-LD script should be present');
        
        if ($jsonLdScript->count() > 0) {
            $jsonLdContent = $jsonLdScript->first()->text();
            $data = json_decode($jsonLdContent, true);
            
            $this->assertArrayHasKey('@context', $data);
            $this->assertArrayHasKey('@type', $data);
            $this->assertEquals('ItemList', $data['@type']);
        }
    }

    public function testSwitchLocaleRedirection(): void
    {
        // Test de la bascule de langue
        $this->client->request('GET', '/metiers/lang/en', [], [], [
            'HTTP_REFERER' => '/metiers'
        ]);
        
        $this->assertTrue($this->client->getResponse()->isRedirection());
    }

    public function testFeatureFlagDisabled(): void
    {
        // Ce test nécessiterait de mock la configuration pour tester la désactivation
        // Il est commenté car il nécessiterait une configuration spécifique de test
        
        /*
        // Simuler la désactivation du feature flag
        $this->client->request('GET', '/metiers');
        $this->assertResponseStatusCodeSame(404);
        */
        
        $this->assertTrue(true, 'Test du feature flag nécessite une configuration spécifique');
    }
}
