<?php

namespace App\Tests\Controller;

use App\Tests\Functional\WebTestCaseWithFixtures;

class SitemapControllerTest extends WebTestCaseWithFixtures
{
    public function testSitemapReturnsXml(): void
    {
        $this->client->request('GET', '/sitemap.xml');
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/xml');
    }

    public function testSitemapContainsUrls(): void
    {
        $this->client->request('GET', '/sitemap.xml');
        $this->assertResponseIsSuccessful();
        
        $content = $this->client->getResponse()->getContent();
        
        $this->assertStringContainsString('<urlset', $content);
        $this->assertStringContainsString('<loc>', $content);
        $this->assertStringContainsString('</url>', $content);
    }

    public function testSitemapIsValidXml(): void
    {
        $this->client->request('GET', '/sitemap.xml');
        $this->assertResponseIsSuccessful();
        
        $content = $this->client->getResponse()->getContent();
        
        // Verifier que c'est du XML valide
        $xml = @simplexml_load_string($content);
        $this->assertNotFalse($xml, 'Le sitemap devrait etre du XML valide');
    }

    public function testSitemapContainsMainPages(): void
    {
        $this->client->request('GET', '/sitemap.xml');
        $this->assertResponseIsSuccessful();
        
        $content = $this->client->getResponse()->getContent();
        
        // Devrait contenir le domaine
        $this->assertStringContainsString('infpf', $content);
    }
}
