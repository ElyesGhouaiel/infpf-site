<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests des pages legales (CGV, Mentions, RGPD, etc.)
 */
class LegalPagesTest extends WebTestCase
{
    /**
     * @dataProvider legalPagesProvider
     */
    public function testLegalPageIsAccessible(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }

    public function legalPagesProvider(): array
    {
        return [
            'CGV' => ['/cgv'],
            'Mentions legales' => ['/mentions-legales'],
            'Reglement interieur' => ['/reglement-interieur'],
            'RGPD' => ['/rgpd'],
            'Contact' => ['/contactez-nous'],
            'Ecole' => ['/ecole'],
            'Metiers' => ['/metiers'],
        ];
    }

    public function testCgvPageContainsConditions(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/cgv');
        $this->assertResponseIsSuccessful();
        
        $content = strtolower($crawler->filter('body')->text());
        $this->assertStringContainsString('condition', $content);
    }

    public function testMentionsLegalesContainsINFPF(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mentions-legales');
        $this->assertResponseIsSuccessful();
        
        $content = $crawler->filter('body')->text();
        $this->assertStringContainsString('INFPF', $content);
    }

    public function testRgpdPageContainsDataInfo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rgpd');
        $this->assertResponseIsSuccessful();
        
        $content = strtolower($crawler->filter('body')->text());
        $this->assertTrue(
            str_contains($content, 'données') || 
            str_contains($content, 'cookie') ||
            str_contains($content, 'protection')
        );
    }

    public function testContactPageHasForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contactez-nous');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testEcolePageContainsSchoolInfo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/ecole');
        $this->assertResponseIsSuccessful();
        
        $content = $crawler->filter('body')->text();
        $this->assertStringContainsString('INFPF', $content);
    }
}
