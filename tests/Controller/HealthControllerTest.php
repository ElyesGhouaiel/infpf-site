<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthControllerTest extends WebTestCase
{
    public function testHealthEndpointReturnsJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testHealthEndpointReturnsStatus(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');
        $this->assertResponseIsSuccessful();
        
        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('status', $content);
    }

    public function testHealthPingEndpoint(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health/ping');
        
        $this->assertResponseIsSuccessful();
        $this->assertEquals('pong', $client->getResponse()->getContent());
    }
}
