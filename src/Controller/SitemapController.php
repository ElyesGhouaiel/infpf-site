<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use App\Repository\BlogRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function index(
        FormationRepository $formationRepository,
        BlogRepository $blogRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $urls = [];
        $hostname = 'https://infpf.fr';
        $today = date('Y-m-d');

        // Pages statiques principales
        $staticPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => '/formation', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => '/ecole', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => '/metiers', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/contactez-nous', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/mentions-legales', 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => '/cgv', 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => '/reglement-interieur', 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => '/avis', 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['loc' => '/guide', 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['loc' => '/indicateurs-resultats', 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['loc' => '/accessibilite-handicap', 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['loc' => '/reclamations', 'priority' => '0.4', 'changefreq' => 'yearly'],
            ['loc' => '/qualiopi', 'priority' => '0.6', 'changefreq' => 'yearly'],
        ];

        // Pages École
        $ecolePages = [
            '/formationadistanceetenligne',
            '/pourquoi-choisir-le-infpf',
            '/notre-methode-apprentissage',
            '/nos-cours-par-correspondance',
            '/coach-personnel',
            '/notre-equipe-pedagogique',
            '/certification-qaliopi-2',
            '/financer-ma-formation',
            '/formations-eligibles-cpf',
        ];

        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => $hostname . $page['loc'],
                'lastmod' => $today,
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ];
        }

        foreach ($ecolePages as $page) {
            $urls[] = [
                'loc' => $hostname . $page,
                'lastmod' => $today,
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        // Formations actives uniquement
        $formations = $formationRepository->findAllActive();
        foreach ($formations as $formation) {
            $urls[] = [
                'loc' => $hostname . '/formation/' . $formation->getId(),
                'lastmod' => $today,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // Toutes les catégories
        $categories = $categoryRepository->findAll();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => $hostname . '/category/' . $category->getId(),
                'lastmod' => $today,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        // Tous les articles de blog publiés
        $blogs = $blogRepository->findBy(['status' => 'published'], ['id' => 'DESC']);
        foreach ($blogs as $blog) {
            $urls[] = [
                'loc' => $hostname . '/blog/' . $blog->getId(),
                'lastmod' => $today,
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        // Générer le XML
        $response = new Response();
        $response->headers->set('Content-Type', 'application/xml');

        return $this->render('sitemap/sitemap.xml.twig', [
            'urls' => $urls,
        ], $response);
    }
}
