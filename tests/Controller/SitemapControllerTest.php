<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SitemapControllerTest extends WebTestCase
{
    public function testSitemapReturnsXml(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/xml');
    }

    public function testSitemapContainsHomepage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');

        $content = $client->getResponse()->getContent();
        
        $this->assertStringContainsString('https://infpf.fr/', $content);
        $this->assertStringContainsString('<urlset', $content);
        $this->assertStringContainsString('<loc>', $content);
        $this->assertStringContainsString('<lastmod>', $content);
    }

    public function testSitemapContainsStaticPages(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');

        $content = $client->getResponse()->getContent();
        
        $expectedPages = [
            '/formation',
            '/ecole',
            '/blog',
            '/contact',
        ];

        foreach ($expectedPages as $page) {
            $this->assertStringContainsString($page, $content, "Le sitemap devrait contenir $page");
        }
    }

    public function testSitemapIsValidXml(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');

        $content = $client->getResponse()->getContent();
        
        // Vérifier que c'est du XML valide
        $xml = @simplexml_load_string($content);
        $this->assertNotFalse($xml, 'Le sitemap devrait être du XML valide');
    }

    public function testSitemapHasCacheHeaders(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');

        // Vérifier les headers de cache
        $response = $client->getResponse();
        $this->assertTrue(
            $response->headers->hasCacheControlDirective('s-maxage') ||
            $response->headers->hasCacheControlDirective('max-age'),
            'Le sitemap devrait avoir des headers de cache'
        );
    }
}
