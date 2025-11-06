<?php
/**
 * Script de vérification OPcache pour Hostinger
 * Accessible via : https://dev.infpf.fr/opcache-check.php
 * 
 * ⚠️ À SUPPRIMER EN PRODUCTION pour des raisons de sécurité
 */

header('Content-Type: application/json; charset=utf-8');

$result = [
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'opcache' => [
        'enabled' => false,
        'version' => null,
        'status' => null,
        'configuration' => null,
        'scripts' => null
    ],
    'php' => [
        'version' => PHP_VERSION,
        'sapi' => PHP_SAPI,
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time')
    ],
    'extensions' => [
        'zend_opcache' => extension_loaded('Zend OPcache'),
        'apcu' => extension_loaded('apcu'),
        'redis' => extension_loaded('redis'),
        'memcached' => extension_loaded('memcached')
    ]
];

// Vérification OPcache
if (extension_loaded('Zend OPcache')) {
    $result['opcache']['enabled'] = true;
    $result['opcache']['version'] = phpversion('Zend OPcache');
    
    // Statut OPcache
    if (function_exists('opcache_get_status')) {
        $status = @opcache_get_status(false);
        if ($status !== false) {
            $result['opcache']['status'] = [
                'opcache_enabled' => $status['opcache_enabled'] ?? false,
                'cache_full' => $status['cache_full'] ?? false,
                'restart_pending' => $status['restart_pending'] ?? false,
                'restart_in_progress' => $status['restart_in_progress'] ?? false,
                'memory_usage' => [
                    'used_memory' => $status['memory_usage']['used_memory'] ?? 0,
                    'free_memory' => $status['memory_usage']['free_memory'] ?? 0,
                    'wasted_memory' => $status['memory_usage']['wasted_memory'] ?? 0,
                    'current_wasted_percentage' => $status['memory_usage']['current_wasted_percentage'] ?? 0
                ],
                'statistics' => [
                    'num_cached_scripts' => $status['opcache_statistics']['num_cached_scripts'] ?? 0,
                    'num_cached_keys' => $status['opcache_statistics']['num_cached_keys'] ?? 0,
                    'max_cached_keys' => $status['opcache_statistics']['max_cached_keys'] ?? 0,
                    'hits' => $status['opcache_statistics']['hits'] ?? 0,
                    'misses' => $status['opcache_statistics']['misses'] ?? 0,
                    'hit_rate' => round(($status['opcache_statistics']['opcache_hit_rate'] ?? 0), 2) . '%'
                ]
            ];
        }
    }
    
    // Configuration OPcache
    if (function_exists('opcache_get_configuration')) {
        $config = @opcache_get_configuration();
        if ($config !== false && isset($config['directives'])) {
            $result['opcache']['configuration'] = [
                'enable' => $config['directives']['opcache.enable'] ?? false,
                'enable_cli' => $config['directives']['opcache.enable_cli'] ?? false,
                'memory_consumption' => $config['directives']['opcache.memory_consumption'] ?? 0,
                'interned_strings_buffer' => $config['directives']['opcache.interned_strings_buffer'] ?? 0,
                'max_accelerated_files' => $config['directives']['opcache.max_accelerated_files'] ?? 0,
                'revalidate_freq' => $config['directives']['opcache.revalidate_freq'] ?? 0,
                'validate_timestamps' => $config['directives']['opcache.validate_timestamps'] ?? false,
                'save_comments' => $config['directives']['opcache.save_comments'] ?? false,
                'file_cache' => $config['directives']['opcache.file_cache'] ?? null
            ];
        }
    }
}

// Informations additionnelles
$result['recommendations'] = [];

if (!$result['opcache']['enabled']) {
    $result['recommendations'][] = '❌ OPcache n\'est PAS activé. Contactez le support Hostinger pour l\'activer.';
} else {
    $result['recommendations'][] = '✅ OPcache est activé et fonctionnel !';
    
    if (isset($result['opcache']['status']['statistics']['hit_rate'])) {
        $hitRate = (float) str_replace('%', '', $result['opcache']['status']['statistics']['hit_rate']);
        if ($hitRate < 90) {
            $result['recommendations'][] = "⚠️ Taux de hit OPcache faible ($hitRate%). Redémarrez le serveur ou contactez Hostinger.";
        } else {
            $result['recommendations'][] = "✅ Excellent taux de hit OPcache ($hitRate%) !";
        }
    }
}

if (!$result['extensions']['apcu']) {
    $result['recommendations'][] = 'ℹ️ APCu n\'est pas disponible (optionnel, mais améliorerait les performances).';
}

if (!$result['extensions']['redis'] && !$result['extensions']['memcached']) {
    $result['recommendations'][] = 'ℹ️ Redis/Memcached non disponibles sur Hostinger (utilisation d\'OPcache uniquement).';
}

// Affichage
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);




