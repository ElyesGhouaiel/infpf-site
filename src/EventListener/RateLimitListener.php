<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RateLimitListener
{
    public function __construct(
        private RateLimiterFactory $contactFormLimiter,
        private RateLimiterFactory $strictLimiter
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        
        // Ne rate limit que les requêtes principales
        if (!$event->isMainRequest()) {
            return;
        }

        // Obtenir l'IP du client
        $ip = $request->getClientIp();
        
        // Rate limiting pour le formulaire de contact
        if ($request->getPathInfo() === '/contactez-nous' && $request->isMethod('POST')) {
            $limiter = $this->contactFormLimiter->create($ip);
            $limit = $limiter->consume(1);

            if (!$limit->isAccepted()) {
                throw new TooManyRequestsHttpException(
                    $limit->getRetryAfter()->getTimestamp() - time(),
                    'Trop de tentatives. Veuillez réessayer dans quelques minutes.'
                );
            }

            // Ajouter les headers rate limit
            $headers = [
                'X-RateLimit-Limit' => $limit->getLimit(),
                'X-RateLimit-Remaining' => $limit->getRemainingTokens(),
                'X-RateLimit-Reset' => $limit->getRetryAfter()->getTimestamp(),
            ];

            $response = $event->getResponse();
            if ($response) {
                foreach ($headers as $key => $value) {
                    $response->headers->set($key, (string) $value);
                }
            }
        }

        // Rate limiting strict pour toutes les requêtes POST
        if ($request->isMethod('POST')) {
            $limiter = $this->strictLimiter->create($ip);
            $limit = $limiter->consume(1);

            if (!$limit->isAccepted()) {
                throw new TooManyRequestsHttpException(
                    $limit->getRetryAfter()->getTimestamp() - time(),
                    'Trop de requêtes. Veuillez patienter.'
                );
            }
        }
    }
}

