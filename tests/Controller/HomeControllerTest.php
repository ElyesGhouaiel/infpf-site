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
    }

    public function testHomePageHasTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('title');
        
        $title = $crawler->filter('title')->text();
        $this->assertNotEmpty($title);
    }

    public function testHomePageContainsINFPF(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        
        $content = $crawler->filter('body')->text();
        $this->assertStringContainsString('INFPF', $content);
    }

    public function testFormationPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/formation');

        $this->assertResponseIsSuccessful();
    }

    public function testFormationPageHasTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formation');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('title');
    }

    public function testFormationPageWithFilters(): void
    {
        $client = static::createClient();
        $client->request('GET', '/formation?thematique[]=1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('title');
    }

    /**
     * Note : Les security headers sont définis dans .htaccess (Apache)
     * et ne peuvent pas être testés via PHPUnit.
     * Ils doivent être testés manuellement via curl ou securityheaders.com
     */
}
