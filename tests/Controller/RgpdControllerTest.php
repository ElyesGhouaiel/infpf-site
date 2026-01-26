<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RgpdControllerTest extends WebTestCase
{
    public function testRgpdPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/rgpd');

        $this->assertResponseIsSuccessful();
    }

    public function testRgpdPageContainsPrivacyInfo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rgpd');

        $this->assertResponseIsSuccessful();
        
        $content = strtolower($crawler->filter('body')->text());
        $this->assertTrue(
            str_contains($content, 'rgpd') || 
            str_contains($content, 'données') ||
            str_contains($content, 'protection'),
            'La page RGPD devrait mentionner RGPD, données ou protection'
        );
    }

    public function testRgpdPageHasCookiesSection(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rgpd');

        $this->assertResponseIsSuccessful();
        
        $content = strtolower($crawler->filter('body')->text());
        $this->assertStringContainsString('cookie', $content);
    }

    public function testRgpdPageHasContactInfo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rgpd');

        $this->assertResponseIsSuccessful();
        
        $content = strtolower($crawler->filter('body')->text());
        $this->assertTrue(
            str_contains($content, 'contact') || 
            str_contains($content, 'email') ||
            str_contains($content, '@'),
            'La page RGPD devrait contenir des informations de contact'
        );
    }

    public function testRgpdPageTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rgpd');

        $this->assertResponseIsSuccessful();
        
        $title = $crawler->filter('title')->text();
        $this->assertTrue(
            str_contains(strtolower($title), 'rgpd') || 
            str_contains(strtolower($title), 'données') ||
            str_contains(strtolower($title), 'protection'),
            'Le titre devrait mentionner RGPD, données ou protection'
        );
    }

    public function testRgpdPageHasRightsInfo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rgpd');

        $this->assertResponseIsSuccessful();
        
        $content = strtolower($crawler->filter('body')->text());
        $this->assertTrue(
            str_contains($content, 'droit') || str_contains($content, 'rights'),
            'La page RGPD devrait expliquer les droits des utilisateurs'
        );
    }
}
