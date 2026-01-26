<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels de navigation générale sur le site
 */
class NavigationTest extends WebTestCase
{
    /**
     * @dataProvider publicPagesProvider
     */
    public function testPublicPageIsAccessible(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertTrue(
            $client->getResponse()->isSuccessful() || 
            $client->getResponse()->isRedirect(),
            "La page $url devrait être accessible"
        );
    }

    public function publicPagesProvider(): array
    {
        return [
            'Home' => ['/'],
            'Formations' => ['/formation'],
            'Contact' => ['/contactez-nous'],
            'Ecole' => ['/ecole'],
            'Blog' => ['/blog'],
            'Metiers' => ['/metiers'],
            'RGPD' => ['/rgpd'],
            'CGV' => ['/cgv'],
            'Mentions légales' => ['/mentions-legales'],
            'Login' => ['/login'],
            'Sitemap' => ['/sitemap.xml'],
            'Health' => ['/health'],
        ];
    }

    public function testHomePageHasMainSections(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        
        // Vérifier la présence du header
        $this->assertSelectorExists('header, .header, nav');
        
        // Vérifier la présence du footer
        $this->assertSelectorExists('footer, .footer');
    }

    public function testFormationListHasFormations(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formation');

        $this->assertResponseIsSuccessful();
        
        // Devrait avoir des liens vers des formations
        $links = $crawler->filter('a[href*="/formation/"]');
        $this->assertGreaterThan(0, $links->count(), 'La page devrait lister des formations');
    }

    public function testBlogListHasArticles(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/blog');

        $this->assertResponseIsSuccessful();
        
        // Devrait avoir des liens vers des articles
        $links = $crawler->filter('a[href*="/blog/"]');
        $this->assertGreaterThan(0, $links->count(), 'La page devrait lister des articles');
    }

    public function testMetiersListHasMetiers(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/metiers');

        $this->assertResponseIsSuccessful();
        
        // Devrait avoir du contenu sur les métiers
        $content = $crawler->filter('body')->text();
        $this->assertNotEmpty($content);
    }

    public function testHealthCheckReturnsJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testSitemapReturnsXml(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/xml');
    }

    public function testHealthPingEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health/ping');

        $this->assertResponseIsSuccessful();
        $this->assertEquals('pong', $client->getResponse()->getContent());
    }

    public function testNonExistentPageReturns404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/page-qui-nexiste-pas-123456');

        $this->assertResponseStatusCodeSame(404);
    }
}
