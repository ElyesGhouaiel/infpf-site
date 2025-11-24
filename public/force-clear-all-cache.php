<?php
header('Content-Type: text/plain; charset=utf-8');
echo "=== VIDAGE COMPLET DES CACHES ===\n\n";

// 1. OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✓ OPcache vidé\n";
} else {
    echo "✗ OPcache non disponible\n";
}

// 2. APCu
if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "✓ APCu vidé\n";
} else {
    echo "✗ APCu non disponible\n";
}

// 3. Realpath cache
clearstatcache(true);
echo "✓ Realpath cache vidé\n";

// 4. Vérifier le timestamp du template
$templatePath = __DIR__ . '/../templates/content/formation/show.html.twig';
if (file_exists($templatePath)) {
    echo "\n✓ Template existe: " . date('Y-m-d H:i:s', filemtime($templatePath)) . "\n";
} else {
    echo "\n✗ Template non trouvé\n";
}

echo "\n=== FAIT ! Attends 30 secondes puis teste en navigation privée ===\n";
