<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * 🧪 Contrôleur de Test pour Pages d'Erreur
 * 
 * Permet de visualiser les pages d'erreur personnalisées en mode dev
 * sans avoir à passer en mode production
 * 
 * ⚠️ À SUPPRIMER AVANT LA MISE EN PRODUCTION
 */
class TestErrorController extends AbstractController
{
    /**
     * Test de la page d'erreur 404
     * URL : /test/error/404
     */
    #[Route('/test/error/404', name: 'test_error_404')]
    public function testError404(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [
            'status_code' => 404,
            'status_text' => 'Not Found',
        ]);
    }

    /**
     * Test de la page d'erreur 500
     * URL : /test/error/500
     */
    #[Route('/test/error/500', name: 'test_error_500')]
    public function testError500(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error500.html.twig', [
            'status_code' => 500,
            'status_text' => 'Internal Server Error',
        ]);
    }

    /**
     * Test de la page d'erreur 403
     * URL : /test/error/403
     */
    #[Route('/test/error/403', name: 'test_error_403')]
    public function testError403(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error403.html.twig', [
            'status_code' => 403,
            'status_text' => 'Forbidden',
        ]);
    }

    /**
     * Test de la page d'erreur générique
     * URL : /test/error/generic
     */
    #[Route('/test/error/generic', name: 'test_error_generic')]
    public function testErrorGeneric(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
            'status_code' => 418,
            'status_text' => "I'm a teapot",
        ]);
    }
}

