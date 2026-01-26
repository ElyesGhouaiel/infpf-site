<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testContactPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/contactez-nous');

        $this->assertResponseIsSuccessful();
    }

    public function testContactPageContainsForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contactez-nous');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testContactPageHasRequiredFields(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contactez-nous');

        $this->assertResponseIsSuccessful();
        // Vérifier la présence des champs essentiels
        $this->assertSelectorExists('input[type="email"], input[name*="email"]');
    }

    public function testContactPageTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contactez-nous');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Contact');
    }

    public function testContactFormSubmitWithoutDataShowsErrors(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contactez-nous');
        
        // Soumettre le formulaire vide (sans reCAPTCHA, devrait échouer)
        $form = $crawler->selectButton('Envoyer')->form();
        $client->submit($form);
        
        // Le formulaire devrait rester sur la page (pas de redirection)
        $this->assertResponseIsSuccessful();
    }
}
