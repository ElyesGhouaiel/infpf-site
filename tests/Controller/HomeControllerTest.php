<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests d'intégration pour HomeController
 */
class HomeControllerTest extends WebTestCase
{
    public function testHomePageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Formation Professionnelle');
    }

    public function testFormationPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/formation');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Formations');
    }

    public function testFormationPageWithFilters(): void
    {
        $client = static::createClient();
        $client->request('GET', '/formation?thematique[]=1');

        $this->assertResponseIsSuccessful();
        // Le titre devrait contenir des informations sur le filtre
        $this->assertSelectorExists('title');
    }

    public function testSecurityHeadersArePresent(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $response = $client->getResponse();
        
        // Vérifier que les headers de sécurité sont présents
        $this->assertTrue($response->headers->has('X-Frame-Options'));
        $this->assertTrue($response->headers->has('X-Content-Type-Options'));
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
    }
}

