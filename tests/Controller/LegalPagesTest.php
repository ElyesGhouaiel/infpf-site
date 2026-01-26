<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests pour les pages légales (CGV, Mentions légales, Règlement intérieur)
 */
class LegalPagesTest extends WebTestCase
{
    /**
     * @dataProvider legalPagesProvider
     */
    public function testLegalPageIsAccessible(string $url, string $expectedContent): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        
        $content = strtolower($crawler->filter('body')->text());
        $this->assertStringContainsString($expectedContent, $content);
    }

    public function legalPagesProvider(): array
    {
        return [
            'CGV' => ['/cgv', 'condition'],
            'Mentions légales' => ['/mentions-legales', 'mention'],
            'Règlement intérieur' => ['/reglement-interieur', 'règlement'],
        ];
    }

    public function testCgvPageHasCompanyInfo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/cgv');

        $this->assertResponseIsSuccessful();
        
        $content = strtolower($crawler->filter('body')->text());
        $this->assertStringContainsString('infpf', $content);
    }

    public function testMentionsLegalesHasLegalInfo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/mentions-legales');

        $this->assertResponseIsSuccessful();
        
        $content = strtolower($crawler->filter('body')->text());
        // Devrait contenir des informations légales
        $this->assertTrue(
            str_contains($content, 'éditeur') || 
            str_contains($content, 'hébergeur') ||
            str_contains($content, 'siret') ||
            str_contains($content, 'directeur'),
            'La page devrait contenir des informations légales obligatoires'
        );
    }

    public function testReglementInterieurExists(): void
    {
        $client = static::createClient();
        $client->request('GET', '/reglement-interieur');

        $this->assertResponseIsSuccessful();
    }
}
