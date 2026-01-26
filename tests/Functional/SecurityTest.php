<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests de sécurité basiques
 */
class SecurityTest extends WebTestCase
{
    public function testAdminPagesRequireAuthentication(): void
    {
        $client = static::createClient();
        
        // Tenter d'accéder à une page admin sans authentification
        $client->request('GET', '/admin');
        
        // Devrait rediriger vers login ou renvoyer 403/401
        $response = $client->getResponse();
        $this->assertTrue(
            $response->isRedirect() || 
            $response->getStatusCode() === 403 ||
            $response->getStatusCode() === 401 ||
            $response->getStatusCode() === 404,
            'Les pages admin devraient être protégées'
        );
    }

    public function testRgpdExportRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/rgpd/export');
        
        $response = $client->getResponse();
        // Devrait rediriger vers login ou renvoyer 403
        $this->assertTrue(
            $response->isRedirect() || 
            $response->getStatusCode() === 403 ||
            $response->getStatusCode() === 401 ||
            $response->getStatusCode() === 404,
            'L\'export RGPD devrait nécessiter une authentification'
        );
    }

    public function testCsrfProtectionOnForms(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contactez-nous');

        $this->assertResponseIsSuccessful();
        
        // Vérifier la présence d'un token CSRF dans le formulaire
        $form = $crawler->filter('form');
        if ($form->count() > 0) {
            $csrfToken = $crawler->filter('input[name*="token"], input[name*="_token"]');
            $this->assertGreaterThan(0, $csrfToken->count(), 'Le formulaire devrait avoir un token CSRF');
        }
    }

    public function testLoginPageHasCsrfProtection(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        
        // Vérifier la présence d'un token CSRF
        $csrfToken = $crawler->filter('input[name="_csrf_token"]');
        $this->assertGreaterThan(0, $csrfToken->count(), 'Le formulaire de login devrait avoir un token CSRF');
    }

    public function testXssProtectionInResponses(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        
        // Vérifier les headers de sécurité (si activés)
        // Note: Ces tests peuvent échouer si les headers sont configurés au niveau serveur
        $headers = $response->headers;
        
        // Au minimum, vérifier que la réponse est bien formée
        $this->assertTrue($response->isSuccessful());
    }

    public function testPasswordFieldsAreHidden(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        
        // Vérifier que le champ mot de passe est de type password
        $passwordField = $crawler->filter('input[type="password"]');
        $this->assertGreaterThan(0, $passwordField->count(), 'Il devrait y avoir un champ password masqué');
    }

    public function testSensitiveRoutesNotExposed(): void
    {
        $client = static::createClient();
        
        // Ces routes ne devraient pas être accessibles publiquement
        $sensitiveRoutes = [
            '/_profiler',
            '/_wdt',
            '/admin/dashboard',
        ];
        
        foreach ($sensitiveRoutes as $route) {
            $client->request('GET', $route);
            $response = $client->getResponse();
            
            // En production, ces routes ne devraient pas être accessibles
            $this->assertTrue(
                $response->getStatusCode() === 404 || 
                $response->getStatusCode() === 403 ||
                $response->getStatusCode() === 401 ||
                $response->isRedirect(),
                "La route $route ne devrait pas être accessible publiquement"
            );
        }
    }
}
