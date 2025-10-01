<?php
// src/Controller/ContactController.php
namespace App\Controller;

use App\Entity\Message;
use App\Form\ContactFormType;
use App\Service\MailService;
use App\Service\NativeMailService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    private $mailService;
    private $nativeMailService;
    private $logger;

    public function __construct(MailService $mailService, NativeMailService $nativeMailService, LoggerInterface $logger)
    {
        $this->mailService = $mailService;
        $this->nativeMailService = $nativeMailService;
        $this->logger = $logger;
    }

    #[Route('/contactez-nous', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Log simple dans un fichier avec chemin absolu
        $logFile = __DIR__ . '/../../var/log/contact_debug.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - DÉBUT FORMULAIRE CONTACT\n", FILE_APPEND);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - User-Agent: " . ($request->headers->get('User-Agent') ?? 'Unknown') . "\n", FILE_APPEND);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - IP: " . ($request->getClientIp() ?? 'Unknown') . "\n", FILE_APPEND);
        
        $message = new Message();
        $form = $this->createForm(ContactFormType::class, $message);
        $form->handleRequest($request);

        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Form submitted: " . ($form->isSubmitted() ? 'YES' : 'NO') . "\n", FILE_APPEND);
        
        if ($form->isSubmitted()) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Form valid: " . ($form->isValid() ? 'YES' : 'NO') . "\n", FILE_APPEND);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - FORMULAIRE VALIDE - DÉBUT TRAITEMENT\n", FILE_APPEND);
            
            // Enregistrer le message dans la base de données
            try {
                $entityManager->persist($message);
                $entityManager->flush();
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Message sauvegardé en BDD avec ID: " . $message->getId() . "\n", FILE_APPEND);
            } catch (\Exception $e) {
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Erreur sauvegarde BDD: " . $e->getMessage() . "\n", FILE_APPEND);
            }

            // Récupérer les données du formulaire
            $contactData = $form->getData();
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Données: " . $contactData->getName() . " - " . $contactData->getEmail() . "\n", FILE_APPEND);

            // Envoyer l'email
            $subject = 'Nouveau message de contact';
            $content = $this->renderView('content/contact/email.html.twig', [
                'name' => $contactData->getName(),
                'email' => $contactData->getEmail(),
                'phone' => $contactData->getNumero(),
                'content' => $contactData->getContent(),
            ]);

            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Préparation envoi email...\n", FILE_APPEND);

            try {
                // Utiliser le service d'email natif au lieu de Symfony Mailer
                $this->nativeMailService->sendContactEmail(
                    'contact@infpf.fr',
                    'contact@infpf.fr',
                    $subject,
                    $content
                );
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Email envoyé avec succès via mail() native!\n", FILE_APPEND);
                $this->addFlash('success', 'Votre message a été envoyé avec succès!');
            } catch (\Exception $e) {
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Erreur envoi email: " . $e->getMessage() . "\n", FILE_APPEND);
                $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            }

            file_put_contents($logFile, date('Y-m-d H:i:s') . " - FIN TRAITEMENT FORMULAIRE\n", FILE_APPEND);
            return $this->redirectToRoute('app_contact');
        } else {
            if ($form->isSubmitted()) {
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Form submitted but not valid\n", FILE_APPEND);
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Form errors: " . implode(', ', $errors) . "\n", FILE_APPEND);
            } else {
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Form not submitted\n", FILE_APPEND);
            }
        }

        return $this->render('content/contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
