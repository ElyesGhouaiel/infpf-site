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
        // Fonctionnalité métiers activée par défaut
        // if (!$this->metierService->isMetiersEnabled()) {
        //     throw $this->createNotFoundException('Fonctionnalité métiers non disponible');
        // }

        $metiers = $this->metierService->getMetiersList();
        $formationsCounts = $this->metierService->getFormationsCountByMetier();

        // Filtrer les métiers qui ont une formation associée (approche 1:1)
        $metiersAvecFormations = [];
        foreach ($metiers as $slug => $metier) {
            $formation = $this->metierService->findFormationByMetier($slug);
            if ($formation) {
                $metiersAvecFormations[$slug] = $metier;
                $metiersAvecFormations[$slug]['formations_count'] = 1; // Toujours 1 en approche 1:1
                $metiersAvecFormations[$slug]['formation'] = $formation;
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
        // Fonctionnalité métiers activée par défaut
        // if (!$this->metierService->isMetiersEnabled()) {
        //     throw $this->createNotFoundException('Fonctionnalité métiers non disponible');
        // }

        $metier = $this->metierService->getMetierBySlug($slug);

        if (!$metier) {
            throw $this->createNotFoundException("Le métier '$slug' n'existe pas");
        }

        // Récupérer LA formation spécifique pour ce métier (approche 1:1)
        $formation = $this->metierService->findFormationByMetier($slug);

        if (!$formation) {
            throw $this->createNotFoundException("Aucune formation trouvée pour le métier '$slug'");
        }

        // Récupérer les autres formations liées au métier
        $otherFormations = $this->metierService->findOtherFormationsByMetier($slug);

        // Génération des données structurées JSON-LD pour la formation principale
        $jsonLd = $this->metierService->generateJsonLd($slug, [$formation]);

        return $this->render('metiers/show.html.twig', [
            'metier' => $metier,
            'formation' => $formation, // Formation principale
            'other_formations' => $otherFormations, // Autres formations liées
            'other_formations_count' => count($otherFormations),
            'page_title' => $metier['title'],
            'page_description' => $metier['description'],
            'meta_description' => $metier['meta_description'],
            'json_ld' => $jsonLd,
            'formations_count' => 1
        ]);
    }

}
