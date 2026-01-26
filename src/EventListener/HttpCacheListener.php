<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Ajoute automatiquement les headers de cache HTTP sur les réponses
 * pour optimiser les performances via CDN et cache navigateur
 */
class HttpCacheListener implements EventSubscriberInterface
{
    // Pages à cacher longtemps (1 heure)
    private const CACHEABLE_ROUTES = [
        'app_home',
        'app_ecole_index',
        'app_metiers_index',
        'app_metiers_show',
        'app_blog_index',
        'sitemap',
    ];

    // Pages à cacher moins longtemps (5 minutes)
    private const SHORT_CACHE_ROUTES = [
        'app_formation_show',
        'app_blog_show',
    ];

    // Pages à ne jamais cacher
    private const NO_CACHE_ROUTES = [
        'app_login',
        'app_logout',
        'app_register',
        'app_contact',
        'rgpd_export',
        'rgpd_deletion_request',
        'rgpd_deletion_confirm',
        'health_check',
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -10],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');

        // Ne pas modifier les réponses non-200 ou les redirections
        if (!$response->isSuccessful() || $response->isRedirection()) {
            return;
        }

        // Ne pas modifier si déjà des headers de cache définis
        if ($response->headers->has('Cache-Control') && 
            $response->headers->get('Cache-Control') !== 'no-cache, private') {
            return;
        }

        // Ne pas cacher les requêtes POST ou si utilisateur connecté
        if ($request->isMethod('POST') || $this->isUserAuthenticated($request)) {
            $response->headers->set('Cache-Control', 'no-cache, private');
            return;
        }

        // Routes à ne jamais cacher
        if (in_array($routeName, self::NO_CACHE_ROUTES, true)) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            return;
        }

        // Routes à cacher longtemps (1 heure public, 5 minutes browser)
        if (in_array($routeName, self::CACHEABLE_ROUTES, true)) {
            $response->setSharedMaxAge(3600); // 1 heure pour CDN
            $response->setMaxAge(300); // 5 minutes pour navigateur
            $response->headers->addCacheControlDirective('stale-while-revalidate', 60);
            return;
        }

        // Routes à cacher moins longtemps (5 minutes)
        if (in_array($routeName, self::SHORT_CACHE_ROUTES, true)) {
            $response->setSharedMaxAge(300); // 5 minutes pour CDN
            $response->setMaxAge(60); // 1 minute pour navigateur
            $response->headers->addCacheControlDirective('stale-while-revalidate', 30);
            return;
        }

        // Pages de formation - cache court car contenu peut changer
        if ($routeName === 'app_formation_index' || str_starts_with($routeName ?? '', 'app_formation')) {
            $response->setSharedMaxAge(600); // 10 minutes pour CDN
            $response->setMaxAge(120); // 2 minutes pour navigateur
            return;
        }
    }

    private function isUserAuthenticated($request): bool
    {
        // Vérifier si l'utilisateur est authentifié via le token de session
        $session = $request->getSession();
        if (!$session) {
            return false;
        }
        
        return $session->has('_security_main');
    }
}
