<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FormationControllerTest extends WebTestCase
{
    public function testFormationListIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/formation');
        $this->assertResponseIsSuccessful();
    }

    public function testFormationListContainsFormations(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formation');
        $this->assertResponseIsSuccessful();
        
        $content = strtolower($crawler->filter('body')->text());
        $this->assertStringContainsString('formation', $content);
    }

    public function testFormationListHasTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formation');
        $this->assertResponseIsSuccessful();
        
        $title = $crawler->filter('title')->text();
        $this->assertNotEmpty($title);
    }

    public function testFormationListHasLinks(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formation');
        $this->assertResponseIsSuccessful();
        
        $links = $crawler->filter('a');
        $this->assertGreaterThan(5, $links->count());
    }
}
