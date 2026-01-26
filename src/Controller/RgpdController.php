<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller RGPD pour la gestion des données personnelles
 * Conforme au Règlement Général sur la Protection des Données (RGPD)
 */
#[Route('/rgpd')]
#[IsGranted('ROLE_USER')]
class RgpdController extends AbstractController
{
    /**
     * Export des données personnelles de l'utilisateur (Article 20 RGPD)
     * Droit à la portabilité des données
     */
    #[Route('/export', name: 'rgpd_export', methods: ['GET'])]
    public function exportData(MessageRepository $messageRepository): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        // Collecter toutes les données de l'utilisateur
        $userData = [
            'export_date' => (new \DateTime())->format('c'),
            'export_type' => 'RGPD Article 20 - Portabilité des données',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ],
        ];

        // Récupérer les messages/demandes de contact de l'utilisateur
        $messages = $messageRepository->findBy(['email' => $user->getEmail()]);
        $userData['contact_requests'] = array_map(function ($message) {
            return [
                'id' => $message->getId(),
                'name' => $message->getName(),
                'email' => $message->getEmail(),
                'phone' => $message->getNumero(),
                'content' => $message->getContent(),
                'formation_id' => $message->getFormationId(),
                'formation_name' => $message->getFormationName(),
                'request_type' => $message->getRequestType(),
                'preferred_mode' => $message->getPreferredMode(),
                'created_at' => $message->getCreatedAt()?->format('c'),
            ];
        }, $messages);

        // Créer la réponse JSON téléchargeable
        $response = new JsonResponse($userData);
        $response->headers->set('Content-Disposition', 
            'attachment; filename="infpf_mes_donnees_' . date('Y-m-d') . '.json"'
        );
        
        return $response;
    }

    /**
     * Page de demande de suppression de compte (Article 17 RGPD)
     * Droit à l'effacement ("droit à l'oubli")
     */
    #[Route('/suppression', name: 'rgpd_deletion_request', methods: ['GET'])]
    public function deletionRequestPage(): Response
    {
        return $this->render('rgpd/deletion_request.html.twig');
    }

    /**
     * Traitement de la demande de suppression
     */
    #[Route('/suppression/confirmer', name: 'rgpd_deletion_confirm', methods: ['POST'])]
    public function confirmDeletion(
        Request $request,
        EntityManagerInterface $em,
        MessageRepository $messageRepository
    ): Response {
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        // Vérification CSRF
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_account', $submittedToken)) {
            $this->addFlash('error', 'Token de sécurité invalide. Veuillez réessayer.');
            return $this->redirectToRoute('rgpd_deletion_request');
        }

        // Anonymiser les messages liés (on garde les messages mais on anonymise)
        $messages = $messageRepository->findBy(['email' => $user->getEmail()]);
        foreach ($messages as $message) {
            $message->setName('Utilisateur supprimé');
            $message->setEmail('anonyme@supprime.local');
            $message->setNumero(null);
        }

        // Supprimer l'utilisateur
        $em->remove($user);
        $em->flush();

        // Déconnecter l'utilisateur
        $this->container->get('security.token_storage')->setToken(null);

        $this->addFlash('success', 'Votre compte a été supprimé conformément à votre demande RGPD.');

        return $this->redirectToRoute('app_home');
    }

    /**
     * Page d'information sur les droits RGPD
     */
    #[Route('/mes-droits', name: 'rgpd_rights', methods: ['GET'])]
    public function rights(): Response
    {
        return $this->render('rgpd/rights.html.twig');
    }
}
