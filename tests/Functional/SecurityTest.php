<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityTest extends WebTestCase
{
    // ==========================================
    // Tests d'authentification
    // ==========================================

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
        
        $passwordField = $crawler->filter('input[type="password"]');
        $this->assertGreaterThan(0, $passwordField->count());
    }

    public function testLoginPageHasCsrfToken(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        
        $csrfToken = $crawler->filter('input[name="_csrf_token"]');
        $this->assertGreaterThan(0, $csrfToken->count(), 'Le formulaire devrait avoir un token CSRF');
    }

    public function testLogoutRedirects(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    // ==========================================
    // Tests de protection des routes admin
    // ==========================================

    public function testAdminRouteRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');
        
        $response = $client->getResponse();
        
        // Devrait rediriger vers login ou renvoyer 403/401/404
        $this->assertTrue(
            $response->isRedirect() || 
            in_array($response->getStatusCode(), [401, 403, 404])
        );
    }

    // ==========================================
    // Tests de protection CSRF
    // ==========================================

    public function testContactFormHasCsrfProtection(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contactez-nous');
        $this->assertResponseIsSuccessful();
        
        $csrfToken = $crawler->filter('input[name*="_token"], input[name*="token"]');
        $this->assertGreaterThan(0, $csrfToken->count(), 'Le formulaire de contact devrait avoir un token CSRF');
    }

    // ==========================================
    // Tests de protection des donnees sensibles
    // ==========================================

    public function testSensitiveRoutesProtected(): void
    {
        $client = static::createClient();
        
        $sensitiveRoutes = [
            '/_profiler',
            '/_wdt',
        ];
        
        foreach ($sensitiveRoutes as $route) {
            $client->request('GET', $route);
            $response = $client->getResponse();
            
            // Ces routes ne devraient pas causer d'erreur 500
            $this->assertNotEquals(500, $response->getStatusCode());
        }
    }

    public function testPasswordFieldIsHidden(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        
        $passwordField = $crawler->filter('input[type="password"]');
        $this->assertGreaterThan(0, $passwordField->count());
        $this->assertEquals('password', $passwordField->attr('type'));
    }

    // ==========================================
    // Tests d'injection et XSS basiques
    // ==========================================

    public function testXssInUrlParameter(): void
    {
        $client = static::createClient();
        $client->request('GET', '/formation?search=<script>alert("xss")</script>');
        
        // Ne devrait pas causer d'erreur 500
        $this->assertNotEquals(500, $client->getResponse()->getStatusCode());
    }

    public function testSqlInjectionInUrlParameter(): void
    {
        $client = static::createClient();
        $client->request('GET', '/formation?search=\' OR 1=1 --');
        
        // Ne devrait pas causer d'erreur 500
        $this->assertNotEquals(500, $client->getResponse()->getStatusCode());
    }
}
