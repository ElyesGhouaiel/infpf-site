<?php

namespace App\Tests\Controller;

use App\Tests\Functional\WebTestCaseWithFixtures;

class HealthControllerTest extends WebTestCaseWithFixtures
{
    public function testHealthEndpointReturnsJson(): void
    {
        $this->client->request('GET', '/health');
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testHealthEndpointReturnsStatus(): void
    {
        $this->client->request('GET', '/health');
        $this->assertResponseIsSuccessful();
        
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('status', $content);
    }

    public function testHealthPingEndpoint(): void
    {
        $this->client->request('GET', '/health/ping');
        
        $this->assertResponseIsSuccessful();
        $this->assertEquals('pong', $this->client->getResponse()->getContent());
    }
}
