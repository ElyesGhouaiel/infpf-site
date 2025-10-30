<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

/**
 * Service de gestion des exclusions pour le système d'analytics
 * Détermine si un utilisateur/requête doit être tracké ou non
 */
class AnalyticsExclusionService
{
    private array $config;
    private Security $security;
    private string $environment;

    public function __construct(Security $security, string $environment)
    {
        $this->security = $security;
        $this->environment = $environment;
        
        // Charger la configuration
        $configPath = dirname(__DIR__, 2) . '/config/analytics_config.php';
        $this->config = file_exists($configPath) ? require $configPath : [];
    }

    /**
     * Détermine si la requête actuelle doit être exclue du tracking
     * 
     * @param Request $request
     * @return array ['excluded' => bool, 'reason' => string]
     */
    public function shouldExclude(Request $request): array
    {
        // 1. Vérifier le cookie développeur (opt-out dur)
        if ($this->hasDevCookie($request)) {
            return ['excluded' => true, 'reason' => 'dev_cookie'];
        }

        // 2. Vérifier l'environnement
        if ($this->isExcludedEnvironment()) {
            return ['excluded' => true, 'reason' => 'environment'];
        }

        // 3. Vérifier le sous-domaine
        if ($this->isExcludedSubdomain($request)) {
            return ['excluded' => true, 'reason' => 'subdomain'];
        }

        // 4. Vérifier le rôle de l'utilisateur connecté
        if ($this->hasBlockedRole()) {
            return ['excluded' => true, 'reason' => 'admin_role'];
        }

        // 5. Vérifier si c'est une route privée
        if ($this->isPrivatePath($request)) {
            return ['excluded' => true, 'reason' => 'private_path'];
        }

        // 6. Vérifier l'IP publique
        if ($this->isExcludedIp($request)) {
            return ['excluded' => true, 'reason' => 'excluded_ip'];
        }

        // 7. Vérifier la whitelist des routes publiques (si configurée)
        if (!$this->isAllowedPublicPath($request)) {
            return ['excluded' => true, 'reason' => 'not_in_whitelist'];
        }

        return ['excluded' => false, 'reason' => null];
    }

    /**
     * Vérifie si le cookie développeur est présent
     */
    private function hasDevCookie(Request $request): bool
    {
        $cookieConfig = $this->config['dev_cookie'] ?? [];
        $cookieName = $cookieConfig['name'] ?? 'xeilos_dev';
        $cookieValue = $cookieConfig['value'] ?? '1';

        return $request->cookies->get($cookieName) === $cookieValue;
    }

    /**
     * Vérifie si l'environnement est exclu
     */
    private function isExcludedEnvironment(): bool
    {
        $excludedEnvs = $this->config['excluded_environments'] ?? [];
        return in_array($this->environment, $excludedEnvs);
    }

    /**
     * Vérifie si le sous-domaine est exclu
     */
    private function isExcludedSubdomain(Request $request): bool
    {
        $excludedSubdomains = $this->config['excluded_subdomains'] ?? [];
        $host = $request->getHost();
        
        foreach ($excludedSubdomains as $subdomain) {
            if (str_starts_with($host, $subdomain . '.')) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur connecté a un rôle bloqué
     */
    private function hasBlockedRole(): bool
    {
        $blockedRoles = $this->config['blocked_roles'] ?? [];
        $user = $this->security->getUser();
        
        if (!$user) {
            return false;
        }

        foreach ($blockedRoles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si le chemin est privé
     */
    private function isPrivatePath(Request $request): bool
    {
        $privatePaths = $this->config['private_paths'] ?? [];
        $path = $request->getPathInfo();

        foreach ($privatePaths as $pattern) {
            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'IP est dans la liste d'exclusion
     */
    private function isExcludedIp(Request $request): bool
    {
        $excludedIps = $this->config['excluded_public_ips'] ?? [];
        $clientIp = $this->getClientIp($request);

        return in_array($clientIp, $excludedIps);
    }

    /**
     * Vérifie si le chemin est dans la whitelist (si configurée)
     */
    private function isAllowedPublicPath(Request $request): bool
    {
        $allowedPaths = $this->config['allowed_public_paths'] ?? [];
        
        // Si la whitelist est vide, toutes les routes publiques sont autorisées
        if (empty($allowedPaths)) {
            return true;
        }

        $path = $request->getPathInfo();

        foreach ($allowedPaths as $pattern) {
            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Récupère l'IP réelle du client (gère les proxies/CDN)
     */
    private function getClientIp(Request $request): ?string
    {
        // Ordre de priorité pour récupérer l'IP réelle
        $headers = [
            'HTTP_CF_CONNECTING_IP',  // Cloudflare
            'HTTP_X_REAL_IP',         // Nginx proxy
            'HTTP_X_FORWARDED_FOR',   // Standard proxy
            'REMOTE_ADDR',            // IP directe
        ];

        foreach ($headers as $header) {
            $ip = $request->server->get($header);
            if ($ip && $this->isValidIp($ip)) {
                // X-Forwarded-For peut contenir plusieurs IPs
                if (str_contains($ip, ',')) {
                    $ips = array_map('trim', explode(',', $ip));
                    $ip = $ips[0]; // Prendre la première (IP cliente)
                }
                return $ip;
            }
        }

        return $request->getClientIp();
    }

    /**
     * Valide qu'une IP est valide et publique
     */
    private function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    /**
     * Retourne la configuration complète
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Anonymise une IP (masque les derniers octets)
     */
    public function anonymizeIp(string $ip): string
    {
        // IPv4
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            $parts[3] = '0'; // Masquer le dernier octet
            return implode('.', $parts);
        }

        // IPv6
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            // Masquer les 4 derniers blocs
            for ($i = 4; $i < 8; $i++) {
                if (isset($parts[$i])) {
                    $parts[$i] = '0';
                }
            }
            return implode(':', $parts);
        }

        return $ip;
    }

    /**
     * Récupère le pays depuis l'IP (géolocalisation serveur)
     */
    public function getCountryFromIp(string $ip): ?string
    {
        $geoConfig = $this->config['geolocation'] ?? [];
        
        if (!($geoConfig['enabled'] ?? true)) {
            return null;
        }

        // Utiliser un service de géolocalisation simple
        // Option 1 : Base de données GeoIP2 (recommandé pour la prod)
        // Option 2 : API externe (ip-api.com, ipinfo.io, etc.)
        
        try {
            // API gratuite ip-api.com (15 requêtes/min max)
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,countryCode");
            if ($response) {
                $data = json_decode($response, true);
                if ($data['status'] === 'success') {
                    return $data['countryCode'] ?? $data['country'] ?? null;
                }
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs de géolocalisation
        }

        return null;
    }
}

