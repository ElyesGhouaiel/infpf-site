#!/usr/bin/env php
<?php
/**
 * Script d'optimisation des images
 * Redimensionne et convertit les images aux bonnes dimensions et en WebP
 */

function optimizeImage(string $sourcePath, int $targetWidth, int $targetHeight, string $outputPath): bool
{
    // Vérifier l'extension
    $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
    
    // Charger l'image source
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $source = @imagecreatefromjpeg($sourcePath);
            break;
        case 'png':
            $source = @imagecreatefrompng($sourcePath);
            break;
        case 'gif':
            $source = @imagecreatefromgif($sourcePath);
            break;
        default:
            echo "❌ Format non supporté: $ext\n";
            return false;
    }
    
    if (!$source) {
        echo "❌ Impossible de charger: $sourcePath\n";
        return false;
    }
    
    // Récupérer les dimensions actuelles
    $currentWidth = imagesx($source);
    $currentHeight = imagesy($source);
    
    // Créer l'image redimensionnée
    $resized = imagecreatetruecolor($targetWidth, $targetHeight);
    
    // Préserver la transparence pour PNG
    if ($ext === 'png') {
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefilledrectangle($resized, 0, 0, $targetWidth, $targetHeight, $transparent);
    }
    
    // Redimensionner
    imagecopyresampled(
        $resized, $source,
        0, 0, 0, 0,
        $targetWidth, $targetHeight,
        $currentWidth, $currentHeight
    );
    
    // Sauvegarder
    $outputExt = pathinfo($outputPath, PATHINFO_EXTENSION);
    
    if ($outputExt === 'webp' && function_exists('imagewebp')) {
        $success = imagewebp($resized, $outputPath, 85);
    } elseif ($outputExt === 'png') {
        $success = imagepng($resized, $outputPath, 9);
    } else {
        $success = imagejpeg($resized, $outputPath, 85);
    }
    
    imagedestroy($source);
    imagedestroy($resized);
    
    return $success;
}

$imgDir = __DIR__ . '/public/img';

// Images à optimiser avec leurs nouvelles dimensions
$optimizations = [
    // Réseaux sociaux (32x32 pour mobile, 64x64 pour desktop 2x)
    [
        'source' => $imgDir . '/Instagram_icon.png',
        'targets' => [
            ['width' => 32, 'height' => 32, 'output' => $imgDir . '/Instagram_icon_32.png'],
            ['width' => 32, 'height' => 32, 'output' => $imgDir . '/Instagram_icon_32.webp'],
        ]
    ],
    [
        'source' => $imgDir . '/Facebook_Logo_2023.png',
        'targets' => [
            ['width' => 32, 'height' => 32, 'output' => $imgDir . '/Facebook_Logo_2023_32.png'],
            ['width' => 32, 'height' => 32, 'output' => $imgDir . '/Facebook_Logo_2023_32.webp'],
        ]
    ],
    [
        'source' => $imgDir . '/free-youtube-logo-icon-2431-thumb.png',
        'targets' => [
            ['width' => 32, 'height' => 32, 'output' => $imgDir . '/free-youtube-logo-icon-2431-thumb_32.png'],
            ['width' => 32, 'height' => 32, 'output' => $imgDir . '/free-youtube-logo-icon-2431-thumb_32.webp'],
        ]
    ],
    // Logo INFPF (150x150 pour mobile, 200x200 pour desktop)
    [
        'source' => $imgDir . '/CROPPED_LOGO__INFPF_2.png',
        'targets' => [
            ['width' => 150, 'height' => 161, 'output' => $imgDir . '/CROPPED_LOGO__INFPF_2_150.png'],
            ['width' => 150, 'height' => 161, 'output' => $imgDir . '/CROPPED_LOGO__INFPF_2_150.webp'],
        ]
    ],
];

$totalSaved = 0;

foreach ($optimizations as $opt) {
    $source = $opt['source'];
    
    if (!file_exists($source)) {
        echo "⚠️  Image source introuvable: $source\n";
        continue;
    }
    
    $originalSize = filesize($source);
    echo "\n📸 " . basename($source) . " (Original: " . number_format($originalSize) . " octets)\n";
    
    foreach ($opt['targets'] as $target) {
        $success = optimizeImage(
            $source,
            $target['width'],
            $target['height'],
            $target['output']
        );
        
        if ($success) {
            $newSize = filesize($target['output']);
            $saved = $originalSize - $newSize;
            $totalSaved += $saved;
            $percentage = round(($saved / $originalSize) * 100, 2);
            
            echo "  ✅ " . basename($target['output']) . " → " 
                 . number_format($newSize) . " octets (-$percentage%)\n";
        } else {
            echo "  ❌ Échec: " . basename($target['output']) . "\n";
        }
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 TOTAL ÉCONOMISÉ: " . number_format($totalSaved) . " octets (~" 
     . round($totalSaved/1024, 2) . " Ko)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";








