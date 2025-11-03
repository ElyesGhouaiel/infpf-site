<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * EventListener pour ajouter les headers de sécurité HTTP
 * Améliore la sécurité du site et le score SecurityHeaders.com / Mozilla Observatory
 */
class SecurityHeadersListener implements EventSubscriberInterface
{
    private string $environment;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        // Ne pas ajouter les headers pour les réponses de profiler en dev
        if ($this->environment === 'dev' && str_contains($event->getRequest()->getPathInfo(), '/_profiler')) {
            return;
        }

        $response = $event->getResponse();

        // ===== HEADERS DE SÉCURITÉ CRITIQUES =====

        // X-Frame-Options: DENY - Empêche le clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // X-Content-Type-Options: nosniff - Empêche le MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection: 1; mode=block - Protection XSS (legacy mais toujours utile)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy: strict-origin-when-cross-origin
        // Contrôle les informations de referrer envoyées
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy (anciennement Feature-Policy)
        // Contrôle quelles fonctionnalités du navigateur peuvent être utilisées
        $permissionsPolicy = [
            'geolocation=()',
            'microphone=()',
            'camera=()',
            'payment=()',
            'usb=()',
            'magnetometer=()',
            'gyroscope=()',
            'speaker=()',
            'vibrate=()',
            'fullscreen=(self)',
            'picture-in-picture=()',
        ];
        $response->headers->set('Permissions-Policy', implode(', ', $permissionsPolicy));

        // Strict-Transport-Security (HSTS) - Seulement en HTTPS
        if ($event->getRequest()->isSecure()) {
            // 1 an de max-age, inclure subdomains, preload optionnel
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Content-Security-Policy (CSP)
        // Politique stricte de sécurité du contenu
        $cspDirectives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://ajax.googleapis.com https://assets.calendly.com https://www.google.com https://www.gstatic.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://assets.calendly.com",
            "img-src 'self' data: https: blob:",
            "font-src 'self' data: https://fonts.gstatic.com",
            "connect-src 'self' https://www.google.com https://www.gstatic.com https://assets.calendly.com",
            "frame-src 'self' https://assets.calendly.com https://www.google.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "upgrade-insecure-requests",
        ];

        // En production, on peut être plus strict
        if ($this->environment === 'prod') {
            // En production, retirer 'unsafe-eval' si possible
            $cspDirectives[1] = str_replace("'unsafe-eval'", "", $cspDirectives[1]);
        }

        $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));

        // Cross-Origin-Embedder-Policy (COEP) - Optionnel mais recommandé
        // $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');

        // Cross-Origin-Opener-Policy (COOP) - Optionnel mais recommandé
        // $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // Cross-Origin-Resource-Policy (CORP) - Optionnel mais recommandé
        // $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
    }
}

