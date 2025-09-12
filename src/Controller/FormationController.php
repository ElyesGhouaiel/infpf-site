<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/formation')]
class FormationController extends AbstractController
{

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
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
        $category = $categoryRepository->findAll();

        return $this->render('content/formation/show.html.twig', [
            'formations' => $formationRepository->find($id),
            'category' => $categoryRepository,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
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
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
