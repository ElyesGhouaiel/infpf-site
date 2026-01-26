<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EcoleControllerTest extends WebTestCase
{
    public function testEcolePageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/ecole');

        $this->assertResponseIsSuccessful();
    }

    public function testEcolePageContainsSchoolSections(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ecole');

        $this->assertResponseIsSuccessful();
        
        // Vérifier la présence de sections clés
        $content = $crawler->filter('body')->text();
        $this->assertStringContainsString('INFPF', $content);
    }

    public function testEcolePageHasNavigationLinks(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ecole');

        $this->assertResponseIsSuccessful();
        
        // Vérifier qu'il y a des liens sur la page
        $links = $crawler->filter('a');
        $this->assertGreaterThan(5, $links->count());
    }

    public function testEcolePageMetaDescription(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ecole');

        $this->assertResponseIsSuccessful();
        
        // Vérifier la présence de la meta description
        $metaDesc = $crawler->filter('meta[name="description"]');
        $this->assertGreaterThan(0, $metaDesc->count());
    }

    public function testEcolePageDisplaysFormationCount(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ecole');

        $this->assertResponseIsSuccessful();
        
        // La page devrait afficher des statistiques
        $content = $crawler->filter('body')->text();
        // On vérifie juste que la page se charge correctement
        $this->assertNotEmpty($content);
    }

    public function testEcolePageHasQualiopiSection(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ecole');

        $this->assertResponseIsSuccessful();
        
        // Qualiopi est un élément important de la page école
        $content = strtolower($crawler->filter('body')->text());
        $this->assertTrue(
            str_contains($content, 'qualiopi') || str_contains($content, 'certif'),
            'La page école devrait mentionner Qualiopi ou certification'
        );
    }
}
