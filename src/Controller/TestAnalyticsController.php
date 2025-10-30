<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestAnalyticsController extends AbstractController
{
    #[Route('/test-analytics', name: 'app_test_analytics')]
    public function index(EntityManagerInterface $em): Response
    {
        $conn = $em->getConnection();
        
        // Vérifier si les tables existent
        $tables = [];
        
        try {
            // Test table analytics
            $stmt = $conn->prepare('SELECT COUNT(*) as count FROM analytics');
            $result = $stmt->executeQuery();
            $analyticsCount = $result->fetchAssociative()['count'];
            $tables['analytics'] = "✅ Table existe ($analyticsCount lignes)";
        } catch (\Exception $e) {
            $tables['analytics'] = "❌ Erreur: " . $e->getMessage();
        }
        
        try {
            // Test table cookie_consent
            $stmt = $conn->prepare('SELECT COUNT(*) as count FROM cookie_consent');
            $result = $stmt->executeQuery();
            $consentCount = $result->fetchAssociative()['count'];
            $tables['cookie_consent'] = "✅ Table existe ($consentCount lignes)";
        } catch (\Exception $e) {
            $tables['cookie_consent'] = "❌ Erreur: " . $e->getMessage();
        }
        
        // Afficher le résultat
        return new Response(
            '<html><body>' .
            '<h1>Test Analytics System</h1>' .
            '<h2>État des tables :</h2>' .
            '<ul>' .
            '<li><strong>analytics:</strong> ' . $tables['analytics'] . '</li>' .
            '<li><strong>cookie_consent:</strong> ' . $tables['cookie_consent'] . '</li>' .
            '</ul>' .
            '<p><a href="/admin/analytics">Aller au dashboard analytics</a></p>' .
            '<hr>' .
            '<h3>Pour créer les tables :</h3>' .
            '<pre>php bin/console doctrine:schema:update --force</pre>' .
            '</body></html>'
        );
    }
}

