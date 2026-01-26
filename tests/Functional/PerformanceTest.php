<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests de performance basiques
 */
class PerformanceTest extends WebTestCase
{
    /**
     * @dataProvider criticalPagesProvider
     */
    public function testPageLoadsInReasonableTime(string $url, int $maxTimeMs): void
    {
        $client = static::createClient();
        
        $startTime = microtime(true);
        $client->request('GET', $url);
        $endTime = microtime(true);
        
        $loadTimeMs = ($endTime - $startTime) * 1000;
        
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirect(),
            "La page $url devrait être accessible"
        );
        
        $this->assertLessThan(
            $maxTimeMs, 
            $loadTimeMs, 
            "La page $url a pris {$loadTimeMs}ms (max attendu: {$maxTimeMs}ms)"
        );
    }

    public function criticalPagesProvider(): array
    {
        return [
            'Home' => ['/', 3000],
            'Formations' => ['/formation', 3000],
            'Health' => ['/health', 1000],
            'Sitemap' => ['/sitemap.xml', 2000],
        ];
    }

    public function testHealthCheckPerformance(): void
    {
        $client = static::createClient();
        
        $times = [];
        for ($i = 0; $i < 5; $i++) {
            $startTime = microtime(true);
            $client->request('GET', '/health/ping');
            $endTime = microtime(true);
            $times[] = ($endTime - $startTime) * 1000;
        }
        
        $avgTime = array_sum($times) / count($times);
        
        $this->assertLessThan(500, $avgTime, "Le health check ping devrait répondre rapidement");
    }

    public function testResponseSizeIsReasonable(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        
        $this->assertResponseIsSuccessful();
        
        $content = $client->getResponse()->getContent();
        $sizeKb = strlen($content) / 1024;
        
        // La page d'accueil ne devrait pas dépasser 500KB de HTML
        $this->assertLessThan(500, $sizeKb, "La page d'accueil fait {$sizeKb}KB (max 500KB)");
    }

    public function testSitemapSizeIsReasonable(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');
        
        $this->assertResponseIsSuccessful();
        
        $content = $client->getResponse()->getContent();
        $sizeKb = strlen($content) / 1024;
        
        // Le sitemap ne devrait pas dépasser 50MB (limite Google)
        // On teste une limite plus raisonnable de 1MB
        $this->assertLessThan(1024, $sizeKb, "Le sitemap fait {$sizeKb}KB (max 1MB)");
    }

    public function testDatabaseQueriesAreEfficient(): void
    {
        $client = static::createClient();
        $client->enableProfiler();
        
        $client->request('GET', '/formation');
        
        $this->assertTrue(
            $client->getResponse()->isSuccessful() || 
            $client->getResponse()->isRedirect()
        );
        
        // Si le profiler est activé, vérifier le nombre de requêtes
        if ($profile = $client->getProfile()) {
            $dbCollector = $profile->getCollector('db');
            if ($dbCollector) {
                $queryCount = $dbCollector->getQueryCount();
                // Pas plus de 50 requêtes pour une page de liste
                $this->assertLessThan(50, $queryCount, "Trop de requêtes DB: $queryCount");
            }
        }
    }
}
