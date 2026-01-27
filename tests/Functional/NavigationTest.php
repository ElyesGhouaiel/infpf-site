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
        
        // Vérifier la présence du header ou navigation
        $hasHeader = $crawler->filter('header, .header, nav, .navbar')->count() > 0;
        $this->assertTrue($hasHeader, 'La page devrait avoir un header ou une navigation');
        
        // Vérifier la présence du footer
        $hasFooter = $crawler->filter('footer, .footer')->count() > 0;
        $this->assertTrue($hasFooter, 'La page devrait avoir un footer');
    }

    public function testFormationPageLoads(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formation');

        $this->assertResponseIsSuccessful();
        
        // La page devrait contenir le mot "formation"
        $content = strtolower($crawler->filter('body')->text());
        $this->assertStringContainsString('formation', $content);
    }

    public function testBlogPageLoads(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/blog');

        $this->assertResponseIsSuccessful();
        
        // La page devrait avoir du contenu
        $content = $crawler->filter('body')->text();
        $this->assertNotEmpty($content);
    }

    public function testMetiersPageLoads(): void
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
