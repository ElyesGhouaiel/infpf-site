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

    public function testSitemapContainsUrls(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');
        $this->assertResponseIsSuccessful();
        
        $content = $client->getResponse()->getContent();
        
        $this->assertStringContainsString('<urlset', $content);
        $this->assertStringContainsString('<loc>', $content);
        $this->assertStringContainsString('</url>', $content);
    }

    public function testSitemapIsValidXml(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');
        $this->assertResponseIsSuccessful();
        
        $content = $client->getResponse()->getContent();
        
        $xml = @simplexml_load_string($content);
        $this->assertNotFalse($xml, 'Le sitemap devrait etre du XML valide');
    }

    public function testSitemapContainsMainPages(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');
        $this->assertResponseIsSuccessful();
        
        $content = $client->getResponse()->getContent();
        $this->assertStringContainsString('infpf', $content);
    }
}
