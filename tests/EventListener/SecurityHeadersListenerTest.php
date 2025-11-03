<?php

namespace App\Tests\EventListener;

use App\EventListener\SecurityHeadersListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Tests unitaires pour SecurityHeadersListener
 */
class SecurityHeadersListenerTest extends TestCase
{
    private SecurityHeadersListener $listener;

    protected function setUp(): void
    {
        $this->listener = new SecurityHeadersListener('prod');
    }

    public function testSecurityHeadersAreAdded(): void
    {
        $request = Request::create('/');
        $response = new Response();
        
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response);
        
        $this->listener->onKernelResponse($event);
        
        $headers = $response->headers;
        
        // Vérifier que les headers de sécurité sont présents
        $this->assertTrue($headers->has('X-Frame-Options'));
        $this->assertEquals('DENY', $headers->get('X-Frame-Options'));
        
        $this->assertTrue($headers->has('X-Content-Type-Options'));
        $this->assertEquals('nosniff', $headers->get('X-Content-Type-Options'));
        
        $this->assertTrue($headers->has('X-XSS-Protection'));
        $this->assertEquals('1; mode=block', $headers->get('X-XSS-Protection'));
        
        $this->assertTrue($headers->has('Referrer-Policy'));
        $this->assertEquals('strict-origin-when-cross-origin', $headers->get('Referrer-Policy'));
        
        $this->assertTrue($headers->has('Permissions-Policy'));
        $this->assertTrue($headers->has('Content-Security-Policy'));
    }

    public function testHstsHeaderOnlyOnHttps(): void
    {
        // Test avec HTTPS
        $request = Request::create('https://example.com/');
        $response = new Response();
        
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response);
        
        $this->listener->onKernelResponse($event);
        
        $this->assertTrue($response->headers->has('Strict-Transport-Security'));
        
        // Test avec HTTP
        $requestHttp = Request::create('http://example.com/');
        $responseHttp = new Response();
        $eventHttp = new ResponseEvent($kernel, $requestHttp, HttpKernelInterface::MAIN_REQUEST, $responseHttp);
        
        $this->listener->onKernelResponse($eventHttp);
        
        $this->assertFalse($responseHttp->headers->has('Strict-Transport-Security'));
    }
}

