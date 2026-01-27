<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MetierControllerTest extends WebTestCase
{
    public function testMetiersIndexPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/metiers');
        
        $this->assertResponseIsSuccessful();
    }

    public function testMetiersIndexPageHasContent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/metiers');
        
        $this->assertResponseIsSuccessful();
        
        // La page devrait avoir du contenu
        $content = $crawler->filter('body')->text();
        $this->assertNotEmpty($content);
    }

    public function testMetiersIndexPageHasTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/metiers');
        
        $this->assertResponseIsSuccessful();
        
        $title = $crawler->filter('title')->text();
        $this->assertNotEmpty($title);
    }

    public function testMetierShowPageWithInvalidSlug(): void
    {
        $client = static::createClient();
        $client->request('GET', '/metiers/slug-inexistant-12345');
        
        $this->assertResponseStatusCodeSame(404);
    }

    public function testMetiersPageContainsMetierKeyword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/metiers');
        
        $this->assertResponseIsSuccessful();
        
        // La page devrait mentionner "métier"
        $content = strtolower($crawler->filter('body')->text());
        $this->assertStringContainsString('métier', $content);
    }
}
