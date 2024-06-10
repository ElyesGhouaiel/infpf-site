<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Blog;
use App\Entity\Category;
use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FormationRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DataProviderService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\BlogRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class HomeController extends AbstractController
{
    #[Route('/formation', name: 'app_home')]
    public function index(FormationRepository $formationRepository, BlogRepository $blogRepository, CategoryRepository $categoryRepository, DataProviderService $dataProviderService, Request $request): Response
    {
        $searchTerm = $request->query->get('search', '');
        $sortOrder = $request->query->get('sort', 'asc');
        $selectedCategoryId = $request->query->get('category_id', 'all');
        $categoryIdForQuery = $selectedCategoryId === 'all' ? null : $selectedCategoryId;

        // Récupère les catégories pour le menu déroulant
        $categories = $categoryRepository->findAll();

        // Filtres
        $filterCriteria = [
            'thematique' => $request->query->all('thematique'),
            'lieu' => $request->query->all('lieu'),
            'duration' => $request->query->all('duration'),
            'level' => $request->query->all('level'),
            'language' => $request->query->all('language'),
            'funding' => $request->query->get('funding'),
        ];

        // Applique les filtres additionnels
        $queryBuilder = $formationRepository->findFormationsByCriteria(array_filter($filterCriteria, function($value) { return !is_null($value) && $value !== ''; }));

        // Appliquer l'ordre de tri
        if ($sortOrder === 'asc') {
            $queryBuilder->orderBy('f.priceFormation', 'ASC');
        } else {
            $queryBuilder->orderBy('f.priceFormation', 'DESC');
        }

        $formations = $queryBuilder->getQuery()->getResult();

        // Comptage des formations par catégorie
        $formationsCountByCategory = [];
        foreach ($categories as $category) {
            $formationsCountByCategory[$category->getId()] = $dataProviderService->getTotalFormationsInCategory($category->getId());
        }

        $totalGlobal = array_sum($formationsCountByCategory);

        // Formations regroupées par catégorie, si nécessaire pour l'affichage
        $formationsByCategory = [];
        foreach ($categories as $category) {
            $formationsByCategory[$category->getId()] = [
                'categoryName' => $category->getName(),
                'formations' => $formationRepository->findBy(['category' => $category]),
            ];
        }

        $blogs = $blogRepository->findAll();

        return $this->render('home/formation.html.twig', [
            'formations' => $formations,
            'blogs' => $blogs,
            'formationsByCategory' => $formationsByCategory,
            'formationsCountByCategory' => $formationsCountByCategory,
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
            'totalGlobal' => $totalGlobal
        ]);
    }
    
        #[Route('/submit-comment', name: 'submit_comment', methods: ['POST'])]
    public function submitComment(Request $request, EntityManagerInterface $entityManager): Response {
        $comment = new Comment();
        $comment->setText($request->request->get('comment'));
        $blogId = $request->request->get('blog_id');
        $blog = $entityManager->getRepository(Blog::class)->find($blogId);
    
        if (!$blog) {
            $this->addFlash('error', 'Blog non trouvé.');
            return $this->redirectToRoute('blog_index'); // Redirigez vers une route existante qui est sûre.
        }
    
        $comment->setBlog($blog);
        $entityManager->persist($comment);
        $entityManager->flush();
    
        $this->addFlash('success', 'Votre commentaire a été ajouté.');
        return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
    }
    
    // Vous pouvez intégrer ici la méthode findBySearchTerm si elle est spécifique à vos besoins
    #[Route('/recherche-formation-ajax', name: 'recherche_formation_ajax')]
    public function rechercheAjax(Request $request, FormationRepository $formationRepository, Formations $formations): JsonResponse
    {
        $query = $request->query->get('query', '');
        $formations = $formationRepository->findBySearchTerm($query);
    
        // Renvoie un fragment HTML
        return $this->render('partials/_search_results.html.twig', [
            'formations' => $formations,
        ]);
    }



    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(FormationRepository $formationRepository,BlogRepository $blogRepository, CategoryRepository $categoryRepository): Response
    {
        $blog = $blogRepository->findAll();
        $category = $categoryRepository->findAll();
        $formations = $formationRepository->findAll();

        // Simule un tableau d'ID de catégories vers les chemins d'images
    $categoryImages = [
        // Ici, remplace ces IDs par les vrais IDs de tes catégories et ajuste les chemins des images
        1 => 'ImageForma1.webp',
        2 => 'FinanceImage.jpeg',
        3 => 'AdministratifsImage.jpeg',
        4 => 'InformatiqueImage.jpeg',
        5 => 'DesignImage.jpeg',
        6 => 'ManagementImage.jpeg',
        7 => 'VenteImage.jpeg',
        11 => 'MarketingImage.jpeg',
        12 => 'AutresImage.jpeg',
        // Continue pour chaque catégorie que tu as
    ];
        
        return $this->render('home/home.html.twig', [
            'blog' => $blog,
            'category' => $category,
            'formations' => $formations,
            'categoryImages' => $categoryImages, // Passe le tableau au template
        ]);
    }


#[Route('/download-document/{id}', name: 'download_document')]
public function downloadDocument($id, FormationRepository $formationRepository)
{
    $formation = $formationRepository->find($id);
    if (!$formation) {
        throw $this->createNotFoundException('La formation demandée n\'existe pas.');
    }

    $fileName = preg_replace('/[\s\/\\:*?"<>|]/', '_', $formation->getNameFormation()) . '.pdf';
    $filePath = $this->getParameter('pdf_directory') . '/' . $fileName;

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('Le document n\'existe pas.');
    }

    return $this->file($filePath, $fileName, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
}

//     #[Route('/get-formations-by-category', name: 'get_formations_by_category')]
// public function getFormationsByCategory(Request $request, FormationRepository $formationRepository): Response
// {
//     $categoryId = $request->query->get('category_id');
//     $formations = $formationRepository->findBy(['category' => $categoryId]);
//     return $this->render('home/formation.html.twig', ['formations' => $formations]);
// }


    #[Route('/formations/filtrer', name: 'formations_filtrer', methods: ['GET'])]
    public function filtrer(Request $request, FormationRepository $formationRepository): Response
    {
        // Récupération des paramètres de filtrage depuis la requête
        $thematique = $request->query->get('thematique', []);
        $format = $request->query->get('format', []);
        $niveau = $request->query->get('niveau', []);
        $langue = $request->query->get('langue', []);

        // Appel au repository pour obtenir les formations filtrées selon les critères
        $formations = $formationRepository->findByCriteria($thematique, $format, $niveau, $langue);

        // Retourne les formations filtrées en JSON pour traitement côté client
        return $this->json([
            'formations' => $formations
        ]);
    }


    #[Route('/mentions-legales', name: 'legal_mentions')]
    public function mentionslegales(): Response
    {
        return $this->render('content/footer/mentions_legales.html.twig');
    }

    #[Route('/avis', name: 'avis')]
    public function avis(): Response
    {
        return $this->render('content/footer/avis.html.twig');
    }

    #[Route('/disclaimer', name: 'disclaimer')]
    public function disclaimer(): Response
    {
        return $this->render('content/footer/disclaimer.html.twig');
    }

    #[Route('/cgv', name: 'cgv')]
    public function cgv(): Response
    {
        return $this->render('content/footer/cgv.html.twig');
    }
    
    #[Route('/like/{id}', name: 'blog_like')]
    public function like($id, EntityManagerInterface $entityManager, Request $request): Response {
        $blog = $entityManager->getRepository(Blog::class)->find($id);
        if (!$blog) {
            throw $this->createNotFoundException('Le blog n\'a pas été trouvé.');
        }
    
        $blog->incrementLikes();
        $entityManager->flush();
    
        // Exécute la fonction supplémentaire et attend sa complétion
        $additionalResult = $this->processAdditionalTasks($blog, $request);
    
        // Vérifie si la tâche supplémentaire a réussi avant de continuer
        if (!$additionalResult) {
            $this->addFlash('error', 'Un problème est survenu avec les tâches supplémentaires.');
            return $this->redirectToRoute('blog_show', ['id' => $id]);
        }
    
        // Redirection vers la page du blog avec un paramètre "modal" si demandé
        $showModal = $request->query->get('comment') === 'true' ? 'comment' : '';
        return $this->redirectToRoute('blog_show', ['id' => $id, 'modal' => $showModal]);
    }
    
    private function processAdditionalTasks(Blog $blog, Request $request): bool {
        try {
            // Logique métier additionnelle, par exemple, vérification des droits, mise à jour de statistiques, etc.
            // Simuler une tâche qui pourrait échouer
            if (rand(0, 10) < 2) {  // Simule un échec aléatoire
                throw new \Exception("Échec simulé de la tâche supplémentaire");
            }
            return true;
        } catch (\Exception $e) {
            // Vous pourriez également enregistrer l'erreur dans un système de log si nécessaire
            return false;
        }
    }
    
    


    #[Route('/filter-formations', name: 'filter_formations', methods: ['POST'])]
public function filterFormations(Request $request, FormationRepository $formationRepository): JsonResponse
{
    $filter = $request->request->get('filter');
    $value = $request->request->get('value');

    // Logique pour filtrer les formations en fonction de $filter et $value
    // Par exemple, si $filter est 'thematique' et $value est 'Cash Management'
    $formations = $formationRepository->findByFilter($filter, $value);

    return $this->json([
        // Retourne les données nécessaires, par exemple
        'formations' => array_map(function ($formation) {
            return [
                'name' => $formation->getName(),
                // Autres champs nécessaires
            ];
        }, $formations),
    ]);
}
    // public function renderBase(): array
    // {
    //     $formationRepository = $this->getDoctrine()->getRepository(Formation::class);
    //     $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

    //     $formations = $formationRepository->findAll();
    //     $categories = $categoryRepository->findAll();

    //     return [
    //         'formations' => $formations,
    //         'categories' => $categories,
    //     ];
    // }










//     $searchTerm = $request->query->get('search', '');
//     $sortOrder = $request->query->get('sort', 'asc');
//     $selectedCategoryId = $request->query->get('category_id');

//     // Assurez-vous d'ajouter la méthode findBySearchAndSort dans votre FormationRepository
//     // Cette méthode doit gérer la logique de recherche et de tri
//     if (!empty($selectedCategoryId)) {
//         // Si un ID de catégorie est sélectionné, filtrez également par catégorie
//         $formations = $formationRepository->findBySearchAndSort($searchTerm, $sortOrder, $selectedCategoryId);
//     } else {
//         // Sinon, recherchez et triez sans filtre de catégorie
//         $formations = $formationRepository->findBySearchAndSort($searchTerm, $sortOrder);
//     }

//     $categories = $categoryRepository->findAll();
//     $formationsCountByCategory = [];

//     foreach ($categories as $category) {
//         $formationsCount = $dataProviderService->getTotalFormationsInCategory($category->getId());
//         $formationsCountByCategory[$category->getId()] = $formationsCount;
//     }

//     $formationsByCategory = $this->groupFormationsByCategory($formations);

//     return $this->render('home/formation.html.twig', [
//         'formationsByCategory' => $formationsByCategory,
//         'formationsCountByCategory' => $formationsCountByCategory,
//         'categories' => $categories,
//         'selectedCategoryId' => $selectedCategoryId,
//     ]);
// }

// private function groupFormationsByCategory($formations)
// {
//     $grouped = [];
//     foreach ($formations as $formation) {
//         $categoryId = $formation->getCategory()->getId();
//         if (!array_key_exists($categoryId, $grouped)) {
//             $grouped[$categoryId] = [
//                 'categoryName' => $formation->getCategory()->getName(),
//                 'formations' => []
//             ];
//         }
//         $grouped[$categoryId]['formations'][] = $formation;
//     }
//     return $grouped;
// }
}