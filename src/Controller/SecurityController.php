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
use Psr\Log\LoggerInterface;


class SecurityController extends AbstractController
{
    private $mailService;
    private $logger;

    public function __construct(MailService $mailService, LoggerInterface $logger)
    {
        $this->mailService = $mailService;
        $this->logger = $logger;
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
        $message = new Message();
        $form = $this->createForm(ContactFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer le message dans la base de données
            $entityManager->persist($message);
            $entityManager->flush();

            // Récupérer les données du formulaire
            $contactData = $form->getData();

            // Envoyer l'email
            $subject = 'Nouveau message de contact';
            $content = $this->renderView('content/contact/email.html.twig', [
                'name' => $contactData->getName(),
                'email' => $contactData->getEmail(),
                'phone' => $contactData->getNumero(),
                'content' => $contactData->getContent(),
            ]);

            $this->logger->info('Preparing to send email.');

            try {
                $this->mailService->sendContactEmail(
                    'contact@infpf.fr', // From email address
                    'contact@infpf.fr', // To email address
                    $subject,
                    $content
                );
                $this->logger->info('Email sent successfully.');
                $this->addFlash('success', 'Votre message a été envoyé avec succès!');
            } catch (\Exception $e) {
                $this->logger->error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
                $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_contact');
        } else {
            $this->logger->info('Form is not submitted or not valid.');
        }

        return $this->render('content/contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}