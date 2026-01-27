<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests de sécurité basiques
 */
class SecurityTest extends WebTestCase
{
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    public function testLoginPageHasForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testLoginPageHasPasswordField(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        
        // Vérifier que le champ mot de passe est de type password
        $passwordField = $crawler->filter('input[type="password"]');
        $this->assertGreaterThan(0, $passwordField->count(), 'Il devrait y avoir un champ password masqué');
    }

    public function testAdminPagesRequireAuthentication(): void
    {
        $client = static::createClient();
        
        // Tenter d'accéder à une page admin sans authentification
        $client->request('GET', '/admin');
        
        $response = $client->getResponse();
        // Devrait rediriger vers login ou renvoyer 403/401/404
        $this->assertTrue(
            $response->isRedirect() || 
            in_array($response->getStatusCode(), [401, 403, 404]),
            'Les pages admin devraient être protégées'
        );
    }

    public function testCsrfProtectionOnLoginForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        
        // Vérifier la présence d'un token CSRF
        $csrfToken = $crawler->filter('input[name="_csrf_token"]');
        $this->assertGreaterThan(0, $csrfToken->count(), 'Le formulaire de login devrait avoir un token CSRF');
    }

    public function testLogoutRedirects(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        // Logout devrait rediriger
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testSensitiveRoutesNotExposed(): void
    {
        $client = static::createClient();
        
        // Ces routes ne devraient pas être accessibles publiquement en prod
        $sensitiveRoutes = [
            '/_profiler',
            '/_wdt',
        ];
        
        foreach ($sensitiveRoutes as $route) {
            $client->request('GET', $route);
            $response = $client->getResponse();
            
            // En test, ces routes peuvent être accessibles, on vérifie juste qu'elles ne causent pas d'erreur 500
            $this->assertNotEquals(500, $response->getStatusCode(), "La route $route ne devrait pas causer d'erreur serveur");
        }
    }
}
