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

    /**
     * Note : Les security headers sont définis dans .htaccess (Apache)
     * et ne peuvent pas être testés via PHPUnit.
     * Ils doivent être testés manuellement via curl ou securityheaders.com
     * 
     * Exemple : curl -I https://dev.infpf.fr | grep -i "x-frame-options"
     */
}

