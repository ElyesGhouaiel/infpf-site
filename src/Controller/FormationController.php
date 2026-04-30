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
        
        // Génération de titre unique avec catégorie et ID
        $pageTitle = $formation->getNameFormation();
        if ($formation->getCategory()) {
            $pageTitle .= ' - Formation ' . $formation->getCategory()->getName();
        }
        $pageTitle .= ' - INFPF';
        
        // Génération de meta description unique
        $metaDescription = 'Découvrez la formation ' . $formation->getNameFormation();
        if ($formation->getCategory()) {
            $metaDescription .= ' en ' . $formation->getCategory()->getName();
        }
        if ($formation->getDescriptionFormation()) {
            $description = strip_tags($formation->getDescriptionFormation());
            $description = substr($description, 0, 120);
            $metaDescription .= '. ' . $description;
        }
        $metaDescription .= ' Formation certifiante à distance avec accompagnement personnalisé.';

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
