<?php

namespace App\Controller;

use App\Service\MetierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MetierController extends AbstractController
{
    private MetierService $metierService;

    public function __construct(MetierService $metierService)
    {
        $this->metierService = $metierService;
    }

    /**
     * Page hub des métiers - liste des métiers disponibles
     */
    #[Route('/metiers', name: 'app_metiers_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Vérifier si la fonctionnalité est activée
        if (!$this->metierService->isMetiersEnabled()) {
            throw $this->createNotFoundException('Fonctionnalité métiers non disponible');
        }

        $metiers = $this->metierService->getMetiersList();
        $formationsCounts = $this->metierService->getFormationsCountByMetier();

        // Filtrer les métiers qui ont au moins une formation
        $metiersAvecFormations = [];
        foreach ($metiers as $slug => $metier) {
            $formations = $this->metierService->findFormationsByMetierLimited($slug, 6);
            if (count($formations) > 0) {
                $metiersAvecFormations[$slug] = $metier;
                $metiersAvecFormations[$slug]['formations_count'] = count($formations);
            }
        }

        // Récupérer les vraies statistiques
        $stats = $this->metierService->getFormationStats();

        return $this->render('metiers/index.html.twig', [
            'metiers' => $metiersAvecFormations,
            'page_title' => 'Découvrez les métiers qui recrutent',
            'meta_description' => 'Explorez les métiers d\'avenir et découvrez nos formations pour accéder à l\'emploi de vos rêves. Informations détaillées sur les missions, compétences et salaires.',
            'formation_stats' => $stats
        ]);
    }

    /**
     * Page détail d'un métier
     */
    #[Route('/metiers/{slug}', name: 'app_metiers_show', methods: ['GET'])]
    public function show(string $slug, Request $request): Response
    {
        // Vérifier si la fonctionnalité est activée
        if (!$this->metierService->isMetiersEnabled()) {
            throw $this->createNotFoundException('Fonctionnalité métiers non disponible');
        }

        $metier = $this->metierService->getMetierBySlug($slug);

        if (!$metier) {
            throw $this->createNotFoundException("Le métier '$slug' n'existe pas");
        }

        $formations = $this->metierService->findFormationsByMetierLimited($slug, 6);

        // Maintenant qu'on gère les cas sans formation avec des cartes factices, on peut permettre l'affichage
        // Note: Le template gère maintenant les cas avec 0, 1 ou plus de formations

        // Génération des données structurées JSON-LD
        $jsonLd = $this->metierService->generateJsonLd($slug, $formations);

        return $this->render('metiers/show.html.twig', [
            'metier' => $metier,
            'formations' => $formations,
            'page_title' => $metier['title'],
            'page_description' => $metier['description'],
            'meta_description' => $metier['meta_description'],
            'json_ld' => $jsonLd,
            'formations_count' => count($formations)
        ]);
    }

}
