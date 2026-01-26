<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Controller pour la génération dynamique du sitemap XML
 */
class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function index(
        FormationRepository $formationRepository,
        BlogRepository $blogRepository
    ): Response {
        $urls = [];
        $hostname = 'https://infpf.fr';

        // Pages statiques principales
        $staticPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => '/formation', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/ecole', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => '/metiers', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['loc' => '/contact', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/pourquoi-choisir-infpf', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => '/financer-ma-formation', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => '/notre-methode-apprentissage', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => '/notre-equipe-pedagogique', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => '/formations-eligibles-cpf', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => '/certification-qaliopi-2', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => '/mentions-legales', 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => '/cgv', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => $hostname . $page['loc'],
                'lastmod' => date('Y-m-d'),
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ];
        }

        // Pages de formations dynamiques
        $formations = $formationRepository->findAll();
        foreach ($formations as $formation) {
            $urls[] = [
                'loc' => $hostname . '/formation/' . $formation->getId(),
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // Pages de blog dynamiques
        $blogs = $blogRepository->findBy([], ['publishedAt' => 'DESC']);
        foreach ($blogs as $blog) {
            $lastmod = $blog->getPublishedAt() ? $blog->getPublishedAt()->format('Y-m-d') : date('Y-m-d');
            $urls[] = [
                'loc' => $hostname . '/blog/' . $blog->getId(),
                'lastmod' => $lastmod,
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        // Génération du XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= '    <url>' . "\n";
            $xml .= '        <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
            $xml .= '        <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            $xml .= '        <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            $xml .= '        <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '    </url>' . "\n";
        }

        $xml .= '</urlset>';

        $response = new Response($xml);
        $response->headers->set('Content-Type', 'application/xml');
        
        // Cache de 1 heure
        $response->setSharedMaxAge(3600);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
