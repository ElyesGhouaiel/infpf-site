#!/usr/bin/env php
<?php
/**
 * Script de minification CSS
 * Réduit la taille des fichiers CSS en supprimant espaces, commentaires, etc.
 */

function minifyCSS(string $css): string
{
    // Supprimer les commentaires CSS
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Supprimer les espaces multiples
    $css = preg_replace('/\s+/', ' ', $css);
    
    // Supprimer les espaces autour des caractères spéciaux
    $css = preg_replace('/\s*([{}|:;,])\s*/', '$1', $css);
    
    // Supprimer les points-virgules avant les accolades fermantes
    $css = str_replace(';}', '}', $css);
    
    // Supprimer les espaces en début et fin
    $css = trim($css);
    
    return $css;
}

$cssDir = __DIR__ . '/public/css';
$files = glob($cssDir . '/*.css');

$totalOriginal = 0;
$totalMinified = 0;

foreach ($files as $file) {
    // Ignorer les fichiers déjà minifiés
    if (strpos($file, '.min.css') !== false) {
        continue;
    }
    
    $original = file_get_contents($file);
    $minified = minifyCSS($original);
    
    $originalSize = strlen($original);
    $minifiedSize = strlen($minified);
    $savings = $originalSize - $minifiedSize;
    $percentage = round(($savings / $originalSize) * 100, 2);
    
    $totalOriginal += $originalSize;
    $totalMinified += $minifiedSize;
    
    // Créer le fichier minifié
    $minFile = str_replace('.css', '.min.css', $file);
    file_put_contents($minFile, $minified);
    
    $filename = basename($file);
    echo "✅ {$filename}\n";
    echo "   Original: " . number_format($originalSize) . " octets\n";
    echo "   Minifié:  " . number_format($minifiedSize) . " octets\n";
    echo "   Économie: " . number_format($savings) . " octets (-{$percentage}%)\n\n";
}

$totalSavings = $totalOriginal - $totalMinified;
$totalPercentage = round(($totalSavings / $totalOriginal) * 100, 2);

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 TOTAL\n";
echo "   Original: " . number_format($totalOriginal) . " octets (" . round($totalOriginal/1024, 2) . " Ko)\n";
echo "   Minifié:  " . number_format($totalMinified) . " octets (" . round($totalMinified/1024, 2) . " Ko)\n";
echo "   Économie: " . number_format($totalSavings) . " octets (-{$totalPercentage}%) ~ " . round($totalSavings/1024, 2) . " Ko\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";







