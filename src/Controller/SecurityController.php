<?php

namespace App\Controller;

use App\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\FormationRepository;
use App\Repository\CategoryRepository;
use App\Entity\Message;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{
    private $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }
    
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, FormationRepository $formationRepository, CategoryRepository $categoryRepository): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'category' => $categoryRepository->findAll(),
            'formations' => $formationRepository->findAll(),
        ]);
    }

    
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    
  

    #[Route('/contactez-nous', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactFormType::class, $contactMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer le message dans la base de données
            $entityManager->persist($contactMessage);
            $entityManager->flush();

            // Récupérer les données du formulaire
            $contactData = $form->getData();

            // Envoyer l'email
            $subject = 'Nouveau message de contact';
            $content = $this->renderView('emails/contact.html.twig', [
                'name' => $contactData->getName(),
                'email' => $contactData->getEmail(),
                'phone' => $contactData->getNumero(),
                'message' => $contactData->getContent(),
            ]);

            $this->mailService->sendContactEmail(
                $contactData->getEmail(),
                'contact@infpf.fr',
                $subject,
                $content
            );

            // Afficher un message de confirmation et rediriger
            $this->addFlash('success', 'Votre message a été envoyé avec succès!');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('content/contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }

}