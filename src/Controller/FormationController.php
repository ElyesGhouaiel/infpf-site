<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/formation')]
class FormationController extends AbstractController
{

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être administrateur pour créer une formation.')]
    public function new(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $formations = new Formation();
        $form = $this->createForm(FormationType::class, $formations);
        $form->handleRequest($request);

        $category = $categoryRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formations);
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('content/formation/new.html.twig', [
            'formations' => $formations,
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_show', methods: ['GET'])]
    public function show(Request $request, $id, FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        $formation = $formationRepository->find($id);
        
        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée');
        }

        // Si la formation est inactive (masquée), renvoyer une 404 pour les visiteurs
        if (!$formation->isActive() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException('Formation non disponible');
        }
        
        $category = $categoryRepository->findAll();

        // ===== TITLE SEO (cible 50-60 chars max, Google tronque ~60) =====
        // Suffixe court "| INFPF" (8 chars) pour rester sous la limite
        $baseName = trim($formation->getNameFormation());
        $suffix   = ' | INFPF';
        $maxLen   = 60;

        if (mb_strlen($baseName . $suffix) <= $maxLen) {
            // Nom court : on peut ajouter la catégorie si la place le permet
            $pageTitle = $baseName;
            $remaining = $maxLen - mb_strlen($baseName . $suffix);
            if ($formation->getCategory()) {
                $catSuffix = ' - ' . $formation->getCategory()->getName();
                if (mb_strlen($catSuffix) <= $remaining) {
                    $pageTitle .= $catSuffix;
                }
            }
            $pageTitle .= $suffix;
        } else {
            // Nom long : on tronque proprement sur un espace + ellipse
            $allowed   = $maxLen - mb_strlen($suffix) - 1; // -1 pour l'ellipse
            $truncated = mb_substr($baseName, 0, $allowed);
            $lastSpace = mb_strrpos($truncated, ' ');
            if ($lastSpace !== false && $lastSpace > $allowed * 0.6) {
                $truncated = mb_substr($truncated, 0, $lastSpace);
            }
            $pageTitle = rtrim($truncated) . '…' . $suffix;
        }

        // ===== META DESCRIPTION (cible 150-160 chars, unique par formation) =====
        // On différencie chaque fiche en intégrant les attributs uniques :
        // durée, prix, RNCP, certificateur (qui varient entre Présentiel/Distanciel/Hybride)
        $parts = [];
        $parts[] = 'Formation ' . $baseName;

        $modality = null;
        $name = $baseName;
        foreach (['Présentiel', 'Distanciel', 'Hybride', 'En ligne'] as $m) {
            if (stripos($name, ' - ' . $m) !== false || stripos($name, $m) !== false) {
                $modality = $m;
                break;
            }
        }
        if (!$modality) { $modality = 'à distance'; }

        $details = [];
        if ($formation->getDureeFormation())  { $details[] = $formation->getDureeFormation(); }
        if ($formation->getPriceFormation())  { $details[] = $formation->getPriceFormation() . ' €'; }
        if ($formation->getRncp())            { $details[] = $formation->getRncp(); }
        if ($formation->getCertificateur())   { $details[] = 'certifiée ' . $formation->getCertificateur(); }

        $metaDescription = 'Formation ' . $baseName;
        if (!empty($details)) {
            $metaDescription .= ' (' . implode(', ', $details) . ')';
        }
        $metaDescription .= ' — accompagnement personnalisé INFPF.';

        // Si encore trop court, on ajoute un extrait de la description
        if (mb_strlen($metaDescription) < 100 && $formation->getDescriptionFormation()) {
            $extra = trim(strip_tags($formation->getDescriptionFormation()));
            $extra = preg_replace('/\s+/', ' ', $extra);
            $extra = mb_substr($extra, 0, 155 - mb_strlen($metaDescription) - 1);
            $metaDescription .= ' ' . $extra;
        }

        // Capping strict à 155 chars
        if (mb_strlen($metaDescription) > 155) {
            $metaDescription = mb_substr($metaDescription, 0, 154) . '…';
        }

        return $this->render('content/formation/show_v2.html.twig', [
            'formations' => $formation,
            'category' => $categoryRepository,
            'page_title' => $pageTitle,
            'meta_description' => $metaDescription
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être administrateur pour modifier une formation.')]
    public function edit(Request $request, Formation $formations, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findAll();
        $form = $this->createForm(FormationType::class, $formations);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('content/formation/edit.html.twig', [
            'formations' => $formations,
            'form' => $form,
            'category' => $category
        ]);
    }

    #[Route('/{id}', name: 'app_formation_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous devez être administrateur pour supprimer une formation.')]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
