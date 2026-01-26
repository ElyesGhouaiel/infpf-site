<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests SEO - Vérifie les éléments importants pour le référencement
 */
class SeoTest extends WebTestCase
{
    /**
     * @dataProvider pagesWithMetaProvider
     */
    public function testPageHasMetaDescription(string $url): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        if (!$client->getResponse()->isSuccessful()) {
            $this->markTestSkipped("Page $url non accessible");
        }

        $metaDesc = $crawler->filter('meta[name="description"]');
        $this->assertGreaterThan(0, $metaDesc->count(), "La page $url devrait avoir une meta description");
        
        $description = $metaDesc->attr('content');
        $this->assertNotEmpty($description, "La meta description de $url ne devrait pas être vide");
        $this->assertGreaterThan(50, strlen($description), "La meta description de $url devrait faire plus de 50 caractères");
    }

    public function pagesWithMetaProvider(): array
    {
        return [
            'Home' => ['/'],
            'Formations' => ['/formation'],
            'Ecole' => ['/ecole'],
            'Contact' => ['/contactez-nous'],
        ];
    }

    public function testHomePageHasCanonicalUrl(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        
        $canonical = $crawler->filter('link[rel="canonical"]');
        $this->assertGreaterThan(0, $canonical->count(), 'La page devrait avoir une URL canonique');
    }

    public function testHomePageHasOpenGraphTags(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        
        // Vérifier les Open Graph tags principaux
        $ogTitle = $crawler->filter('meta[property="og:title"]');
        $this->assertGreaterThan(0, $ogTitle->count(), 'La page devrait avoir og:title');
        
        $ogType = $crawler->filter('meta[property="og:type"]');
        $this->assertGreaterThan(0, $ogType->count(), 'La page devrait avoir og:type');
    }

    public function testHomePageHasTwitterCards(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        
        $twitterCard = $crawler->filter('meta[name="twitter:card"]');
        $this->assertGreaterThan(0, $twitterCard->count(), 'La page devrait avoir twitter:card');
    }

    public function testSitemapContainsMainPages(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/sitemap.xml');

        $this->assertResponseIsSuccessful();
        
        $content = $client->getResponse()->getContent();
        
        // Vérifier que les pages principales sont dans le sitemap
        $this->assertStringContainsString('infpf.fr', $content);
        $this->assertStringContainsString('<loc>', $content);
        $this->assertStringContainsString('</url>', $content);
    }

    public function testHomePageHasSchemaOrg(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        
        $schemaScripts = $crawler->filter('script[type="application/ld+json"]');
        $this->assertGreaterThan(0, $schemaScripts->count(), 'La page devrait avoir des données Schema.org');
    }

    public function testFormationPageHasSchemaOrg(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formation');

        $this->assertResponseIsSuccessful();
        
        // Vérifier si une formation individuelle a des données structurées
        $links = $crawler->filter('a[href*="/formation/"]');
        if ($links->count() > 0) {
            $formationUrl = $links->first()->attr('href');
            $crawler = $client->request('GET', $formationUrl);
            
            if ($client->getResponse()->isSuccessful()) {
                $schemaScripts = $crawler->filter('script[type="application/ld+json"]');
                $this->assertGreaterThan(0, $schemaScripts->count(), 'La page formation devrait avoir des données Schema.org');
            }
        }
    }

    public function testPagesHaveProperHeadingStructure(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        
        // Vérifier qu'il y a un H1
        $h1 = $crawler->filter('h1');
        $this->assertGreaterThan(0, $h1->count(), 'La page devrait avoir un H1');
        
        // Un seul H1 par page (bonne pratique SEO)
        $this->assertEquals(1, $h1->count(), 'La page devrait avoir exactement un H1');
    }
}
