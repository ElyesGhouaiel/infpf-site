<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FormationControllerTest extends WebTestCase
{
    public function testFormationIndexIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/formation');

        $this->assertResponseIsSuccessful();
    }

    public function testFormationIndexContainsContent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formation');

        $this->assertResponseIsSuccessful();
        
        // La page formations devrait contenir du contenu
        $content = $crawler->filter('body')->text();
        $this->assertNotEmpty($content);
    }

    public function testFormationPageHasTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formation');

        $this->assertResponseIsSuccessful();
        
        $title = $crawler->filter('title')->text();
        $this->assertNotEmpty($title);
    }

    public function testFormationPageHasFormationKeyword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formation');

        $this->assertResponseIsSuccessful();
        
        // La page devrait mentionner "formation"
        $content = strtolower($crawler->filter('body')->text());
        $this->assertStringContainsString('formation', $content);
    }
}
