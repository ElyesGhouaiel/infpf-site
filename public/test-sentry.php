<?php

/**
 * 🧪 TEST SENTRY - Page de test pour validation de Sentry
 * 
 * Cette page génère volontairement une erreur pour tester Sentry.
 * 
 * ⚠️ À SUPPRIMER EN PRODUCTION !
 * 
 * URL : https://dev.infpf.fr/test-sentry.php
 */

// Charger l'autoloader Symfony
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Charger les variables d'environnement
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(dirname(__DIR__).'/.env');

// Initialiser Sentry
if (!empty($_ENV['SENTRY_DSN'])) {
    \Sentry\init([
        'dsn' => $_ENV['SENTRY_DSN'],
        'environment' => $_ENV['APP_ENV'] ?? 'dev',
        'traces_sample_rate' => 1.0,
    ]);
    
    echo "<h1>🧪 Test Sentry</h1>";
    echo "<p>Sentry DSN configuré : " . substr($_ENV['SENTRY_DSN'], 0, 30) . "...</p>";
    echo "<p><strong>Génération d'une erreur de test...</strong></p>";
    
    // Générer une erreur de test
    try {
        throw new \Exception('🧪 Test Sentry - Erreur volontaire générée le ' . date('Y-m-d H:i:s'));
    } catch (\Exception $e) {
        // Capturer l'exception avec Sentry
        \Sentry\captureException($e);
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h2 style='color: #155724; margin-top: 0;'>✅ Erreur envoyée à Sentry !</h2>";
        echo "<p style='color: #155724;'>Message : <strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>";
        echo "<p style='color: #155724;'>Vérifiez maintenant dans votre tableau de bord Sentry :</p>";
        echo "<a href='https://sentry.io/organizations/infpf/projects/php-symfony/' target='_blank' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Ouvrir Sentry.io</a>";
        echo "</div>";
        
        // Afficher aussi l'erreur pour debug
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h3 style='color: #721c24; margin-top: 0;'>Détails de l'erreur</h3>";
        echo "<pre style='color: #721c24;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    }
    
    echo "<hr>";
    echo "<p><strong>Instructions :</strong></p>";
    echo "<ol>";
    echo "<li>Attendez 10-30 secondes que Sentry traite l'événement</li>";
    echo "<li>Actualisez votre tableau de bord Sentry</li>";
    echo "<li>Vous devriez voir l'erreur apparaître dans la liste</li>";
    echo "<li>⚠️ N'oubliez pas de <strong>supprimer ce fichier</strong> avant la mise en production !</li>";
    echo "</ol>";
    
} else {
    echo "<h1>❌ Erreur</h1>";
    echo "<p>SENTRY_DSN n'est pas configuré dans le fichier .env</p>";
}


