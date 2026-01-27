<?php

namespace App\Tests\Controller;

use App\Tests\Functional\WebTestCaseWithFixtures;

class FormationControllerTest extends WebTestCaseWithFixtures
{
    public function testFormationListIsAccessible(): void
    {
        $this->client->request('GET', '/formation');
        $this->assertResponseIsSuccessful();
    }

    public function testFormationListContainsFormations(): void
    {
        $crawler = $this->client->request('GET', '/formation');
        $this->assertResponseIsSuccessful();
        
        // La page devrait contenir "formation"
        $content = strtolower($crawler->filter('body')->text());
        $this->assertStringContainsString('formation', $content);
    }

    public function testFormationListHasTitle(): void
    {
        $crawler = $this->client->request('GET', '/formation');
        $this->assertResponseIsSuccessful();
        
        $title = $crawler->filter('title')->text();
        $this->assertNotEmpty($title);
    }

    public function testFormationListHasLinks(): void
    {
        $crawler = $this->client->request('GET', '/formation');
        $this->assertResponseIsSuccessful();
        
        // La page devrait avoir des liens
        $links = $crawler->filter('a');
        $this->assertGreaterThan(5, $links->count());
    }
}
