<?php
// Vider OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache vidé\n";
}

// Vider Twig cache
$twigDirs = ['var/cache/prod/twig', 'var/cache/dev/twig'];
foreach ($twigDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "Twig cache $dir vidé\n";
    }
}

echo "Tous les caches vidés !\n";
