<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SeoTest extends WebTestCase
{
    /**
     * @dataProvider pagesWithMetaProvider
     */
    public function testPageHasMetaDescription(string $url): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);
        $this->assertResponseIsSuccessful();

        $metaDesc = $crawler->filter('meta[name="description"]');
        $this->assertGreaterThan(0, $metaDesc->count(), "La page $url devrait avoir une meta description");
    }

    public function pagesWithMetaProvider(): array
    {
        return [
            'Home' => ['/'],
            'Formations' => ['/formation'],
            'Ecole' => ['/ecole'],
        ];
    }

    public function testHomePageHasTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        $title = $crawler->filter('title');
        $this->assertGreaterThan(0, $title->count());
        $this->assertNotEmpty($title->text());
    }

    public function testHomePageHasOpenGraphTags(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        $ogTags = $crawler->filter('meta[property^="og:"]');
        $this->assertGreaterThan(0, $ogTags->count(), 'La page devrait avoir des tags Open Graph');
    }

    public function testHomePageHasH1(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        $h1 = $crawler->filter('h1');
        $this->assertGreaterThan(0, $h1->count(), 'La page devrait avoir au moins un H1');
    }

    public function testFormationPageHasH1(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formation');
        $this->assertResponseIsSuccessful();
        
        $h1 = $crawler->filter('h1');
        $this->assertGreaterThan(0, $h1->count(), 'La page formation devrait avoir un H1');
    }

    public function testPagesHaveProperLanguage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        $html = $crawler->filter('html');
        $lang = $html->attr('lang');
        
        $this->assertNotEmpty($lang);
    }

    public function testPagesHaveViewport(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        $viewport = $crawler->filter('meta[name="viewport"]');
        $this->assertGreaterThan(0, $viewport->count(), 'La page devrait avoir un meta viewport');
    }

    public function testPagesHaveCharset(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        $charset = $crawler->filter('meta[charset], meta[http-equiv="Content-Type"]');
        $this->assertGreaterThan(0, $charset->count(), 'La page devrait declarer un charset');
    }
}
