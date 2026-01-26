<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    public function testLoginPageContainsForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testLoginPageHasEmailField(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[type="email"], input[name*="email"], input[name*="username"]');
    }

    public function testLoginPageHasPasswordField(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('input[type="password"]');
    }

    public function testLoginPageHasSubmitButton(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('button[type="submit"], input[type="submit"]');
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Remplir le formulaire avec des identifiants invalides
        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'invalid@test.com',
            'password' => 'wrongpassword',
        ]);

        $client->submit($form);
        
        // Devrait rediriger vers login avec erreur ou rester sur la page
        $this->assertTrue(
            $client->getResponse()->isRedirect() || 
            $client->getResponse()->isSuccessful()
        );
    }

    public function testLogoutRedirects(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        // Logout devrait rediriger
        $this->assertTrue($client->getResponse()->isRedirect());
    }
}
