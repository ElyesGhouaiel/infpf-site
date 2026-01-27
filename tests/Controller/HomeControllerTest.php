<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

    public function testHomePageContainsINFPF(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        $content = $crawler->filter('body')->text();
        $this->assertStringContainsString('INFPF', $content);
    }

    public function testHomePageHasHeader(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        $hasHeader = $crawler->filter('header, nav, .navbar')->count() > 0;
        $this->assertTrue($hasHeader, 'La page devrait avoir un header');
    }

    public function testHomePageHasFooter(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        $hasFooter = $crawler->filter('footer')->count() > 0;
        $this->assertTrue($hasFooter, 'La page devrait avoir un footer');
    }

    public function testHomePageHasTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        $title = $crawler->filter('title')->text();
        $this->assertNotEmpty($title);
    }
}
