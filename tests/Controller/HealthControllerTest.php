<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthControllerTest extends WebTestCase
{
    public function testHealthCheckReturnsJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testHealthCheckContainsRequiredFields(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('timestamp', $content);
        $this->assertArrayHasKey('checks', $content);
    }

    public function testHealthCheckContainsAllChecks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $content = json_decode($client->getResponse()->getContent(), true);
        $checks = $content['checks'];

        $expectedChecks = ['application', 'database', 'cache', 'logs', 'memory'];

        foreach ($expectedChecks as $check) {
            $this->assertArrayHasKey($check, $checks, "Le check '$check' devrait être présent");
        }
    }

    public function testHealthCheckApplicationHasVersion(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('version', $content['checks']['application']);
        $this->assertArrayHasKey('environment', $content['checks']['application']);
    }

    public function testHealthPingReturnsOk(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health/ping');

        $this->assertResponseIsSuccessful();
        $this->assertEquals('pong', $client->getResponse()->getContent());
    }

    public function testHealthPingIsPlainText(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health/ping');

        $this->assertResponseHeaderSame('content-type', 'text/plain; charset=UTF-8');
    }

    public function testHealthCheckStatusIsHealthyOrDegraded(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertContains(
            $content['status'],
            ['healthy', 'degraded', 'unhealthy'],
            "Le status devrait être 'healthy', 'degraded' ou 'unhealthy'"
        );
    }

    public function testHealthCheckTimestampIsIso8601(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $content = json_decode($client->getResponse()->getContent(), true);

        // Vérifie que le timestamp est au format ISO 8601
        $timestamp = \DateTime::createFromFormat(\DateTime::ATOM, $content['timestamp']);
        $this->assertNotFalse($timestamp, "Le timestamp devrait être au format ISO 8601");
    }
}
