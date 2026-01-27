<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests de performance basiques
 */
class PerformanceTest extends WebTestCase
{
    public function testHomePageLoadsQuickly(): void
    {
        $client = static::createClient();
        
        $startTime = microtime(true);
        $client->request('GET', '/');
        $endTime = microtime(true);
        
        $loadTimeMs = ($endTime - $startTime) * 1000;
        
        $this->assertResponseIsSuccessful();
        
        // La page devrait charger en moins de 5 secondes
        $this->assertLessThan(5000, $loadTimeMs, "La page d'accueil a pris {$loadTimeMs}ms");
    }

    public function testHealthCheckIsQuick(): void
    {
        $client = static::createClient();
        
        $startTime = microtime(true);
        $client->request('GET', '/health');
        $endTime = microtime(true);
        
        $loadTimeMs = ($endTime - $startTime) * 1000;
        
        $this->assertResponseIsSuccessful();
        
        // Le health check devrait répondre en moins de 2 secondes
        $this->assertLessThan(2000, $loadTimeMs, "Le health check a pris {$loadTimeMs}ms");
    }

    public function testSitemapSizeIsReasonable(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');
        
        $this->assertResponseIsSuccessful();
        
        $content = $client->getResponse()->getContent();
        $sizeKb = strlen($content) / 1024;
        
        // Le sitemap ne devrait pas dépasser 1MB
        $this->assertLessThan(1024, $sizeKb, "Le sitemap fait {$sizeKb}KB (max 1MB)");
    }

    public function testFormationPageLoads(): void
    {
        $client = static::createClient();
        
        $startTime = microtime(true);
        $client->request('GET', '/formation');
        $endTime = microtime(true);
        
        $loadTimeMs = ($endTime - $startTime) * 1000;
        
        $this->assertResponseIsSuccessful();
        
        // La page devrait charger en moins de 5 secondes
        $this->assertLessThan(5000, $loadTimeMs, "La page formation a pris {$loadTimeMs}ms");
    }

    public function testHealthPingIsVeryQuick(): void
    {
        $client = static::createClient();
        
        $times = [];
        for ($i = 0; $i < 3; $i++) {
            $startTime = microtime(true);
            $client->request('GET', '/health/ping');
            $endTime = microtime(true);
            $times[] = ($endTime - $startTime) * 1000;
        }
        
        $avgTime = array_sum($times) / count($times);
        
        // Le health ping devrait répondre en moins de 1 seconde en moyenne
        $this->assertLessThan(1000, $avgTime, "Le health ping moyen est {$avgTime}ms");
    }
}
