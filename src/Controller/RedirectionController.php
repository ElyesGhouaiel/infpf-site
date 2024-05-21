<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FormationRepository;
use App\Repository\CategoryRepository;

class RedirectionController extends AbstractController
{
    #[Route('/animaliers', name: 'redirectToAnimaliers')]
    public function redirectToAnimaliers(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/animaliers.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/INFPF-reference-datadock', name: 'redirectToINFPFReferenceDatadock')]
    public function redirectToINFPFReferenceDatadock(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/infpf-reference-datadock.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/certification-qaliopi-2', name: 'redirectToCertificationQaliopi2')]
    public function redirectToCertificationQaliopi2(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/certification-qaliopi-2.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/coach-personnel', name: 'redirectToCoachPersonnel')]
    public function redirectToCoachPersonnel(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/coach-personnel.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/financer-ma-formation', name: 'redirectToFinancerMaFormation')]
    public function redirectToFinancerMaFormation(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/financer-ma-formation.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/formationadistanceetenligne', name: 'redirectToFormationADistanceEtEnLigne')]
    public function redirectToFormationADistanceEtEnLigne(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/formationadistanceetenligne.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/formations-eligibles-cpf', name: 'redirectToFormationsEligiblesCPF')]
    public function redirectToFormationsEligiblesCPF(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/formations-eligibles-cpf.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/nos-cours-par-correspondance', name: 'redirectToNosCoursParCorrespondance')]
    public function redirectToNosCoursParCorrespondance(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/nos-cours-par-correspondance.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/notre-equipe-pedagogique', name: 'redirectToNotreEquipePedagogique')]
    public function redirectToNotreEquipePedagogique(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/notre-equipe-pedagogique.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/notre-methode-apprentissage', name: 'redirectToNotreMethodeApprentissage')]
    public function redirectToNotreMethodeApprentissage(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/notre-methode-apprentissage.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/parrainage-eleve', name: 'redirectToParrainageEleve')]
    public function redirectToParrainageEleve(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/parrainage-eleve.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/pourquoi-choisir-infpf', name: 'redirectToPourquoiChoisirLeInfpf')]
    public function redirectToPourquoiChoisirLeINFPF(FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('content/ecole/pourquoi-choisir-le-infpf.html.twig', [
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }
}