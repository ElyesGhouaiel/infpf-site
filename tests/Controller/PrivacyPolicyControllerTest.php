<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrivacyPolicyControllerTest extends WebTestCase
{
    public function testPrivacyPolicyPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/politique-de-confidentialite');

        $this->assertResponseIsSuccessful();
    }

    public function testPrivacyPolicyPageContainsPrivacyInfo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/politique-de-confidentialite');

        $this->assertResponseIsSuccessful();
        
        $content = strtolower($crawler->filter('body')->text());
        $this->assertTrue(
            str_contains($content, 'confidentialité') || 
            str_contains($content, 'données') ||
            str_contains($content, 'privacy'),
            'La page devrait contenir des informations sur la confidentialité'
        );
    }
}
