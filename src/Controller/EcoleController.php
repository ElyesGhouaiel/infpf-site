<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EcoleController extends AbstractController
{
    /**
     * Page hub de l'École - liste des pages disponibles
     */
    #[Route('/ecole', name: 'app_ecole_index', methods: ['GET'])]
    public function index(): Response
    {
        // Définition des pages de l'école avec leurs informations
        $pagesEcole = [
            'formations-distance' => [
                'title' => 'Nos formations à distance',
                'description' => 'Découvrez l\'excellence de l\'INFPF accessible partout, à tout moment avec nos formations 100% en ligne.',
                'route' => 'redirectToFormationADistanceEtEnLigne',
                'icon' => 'monitor',
                'highlight' => true
            ],
            'pourquoi-choisir' => [
                'title' => 'Pourquoi choisir l\'INFPF ?',
                'description' => 'Les avantages uniques qui font de l\'INFPF le premier choix pour votre formation professionnelle.',
                'route' => 'redirectToPourquoiChoisirLeInfpf',
                'icon' => 'star',
                'highlight' => false
            ],
            'methode-apprentissage' => [
                'title' => 'Notre méthode d\'apprentissage',
                'description' => 'Une pédagogie innovante adaptée aux adultes pour maximiser votre apprentissage à distance.',
                'route' => 'redirectToNotreMethodeApprentissage',
                'icon' => 'book',
                'highlight' => false
            ],
            'cours-correspondance' => [
                'title' => 'Nos cours par correspondance',
                'description' => 'L\'expérience traditionnelle enrichie par les technologies modernes pour un apprentissage optimal.',
                'route' => 'redirectToNosCoursParCorrespondance',
                'icon' => 'mail',
                'highlight' => false
            ],
            'coaching' => [
                'title' => 'Le coaching : un suivi personnel',
                'description' => 'Un accompagnement humain personnalisé avec plus de 25 coachs dédiés à votre réussite.',
                'route' => 'redirectToCoachPersonnel',
                'icon' => 'users',
                'highlight' => false
            ],
            'equipe-pedagogique' => [
                'title' => 'Notre équipe pédagogique',
                'description' => 'Rencontrez les professionnels passionnés qui vous accompagnent dans votre parcours de formation.',
                'route' => 'redirectToNotreEquipePedagogique',
                'icon' => 'graduation-cap',
                'highlight' => false
            ],
            'certification-qualiopi' => [
                'title' => 'L\'INFPF certifié Qualiopi',
                'description' => 'Une certification qui garantit la qualité de nos formations et leur éligibilité aux financements.',
                'route' => 'redirectToCertificationQaliopi2',
                'icon' => 'award',
                'highlight' => true
            ],
            'financements' => [
                'title' => 'Financements et prises en charge',
                'description' => 'Découvrez toutes les solutions pour financer votre formation : CPF, Pôle Emploi, OPCO et plus.',
                'route' => 'redirectToFinancerMaFormation',
                'icon' => 'credit-card',
                'highlight' => false
            ],
            'formations-cpf' => [
                'title' => 'Formations CPF',
                'description' => 'Nos formations éligibles au Compte Personnel de Formation pour un financement simplifié.',
                'route' => 'redirectToFormationsEligiblesCPF',
                'icon' => 'check-circle',
                'highlight' => true
            ]
        ];

        // Statistiques de l'école
        $stats = [
            'years_experience' => '15+',
            'students_trained' => '226K+',
            'satisfaction_rate' => '94%',
            'average_rating' => '9/10'
        ];

        return $this->render('ecole/index.html.twig', [
            'pages_ecole' => $pagesEcole,
            'page_title' => 'L\'École INFPF : Excellence et Innovation Pédagogique',
            'meta_description' => 'Découvrez l\'École INFPF : formations à distance, méthodes d\'apprentissage innovantes, coaching personnalisé et certification Qualiopi. Plus de 15 ans d\'excellence pédagogique.',
            'stats' => $stats
        ]);
    }
}
