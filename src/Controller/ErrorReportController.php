<?php

namespace App\Controller;

use App\Service\NativeMailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class ErrorReportController extends AbstractController
{
    private $nativeMailService;
    private $logger;

    public function __construct(
        NativeMailService $nativeMailService,
        LoggerInterface $logger
    ) {
        $this->nativeMailService = $nativeMailService;
        $this->logger = $logger;
    }

    #[Route('/report-error', name: 'app_report_error', methods: ['POST'])]
    public function reportError(Request $request): Response {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $message = $request->request->get('message');
        $errorCode = $request->request->get('error_code', 'Inconnu');
        $errorUrl = $request->request->get('error_url', 'Non spécifiée');

        // Validation basique
        if (empty($name) || empty($email) || empty($message)) {
            return $this->json([
                'success' => false,
                'message' => 'Tous les champs sont obligatoires.'
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                'success' => false,
                'message' => 'Email invalide.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Générer le contenu HTML de l'email
            $emailContent = $this->renderEmailContent($name, $email, $message, $errorCode, $errorUrl);
            $subject = "🔴 Erreur {$errorCode} signalée sur INFPF";

            // Envoyer l'email avec le service natif (comme le formulaire de contact)
            // Le Reply-To est configuré sur l'email du visiteur pour répondre directement
            $this->nativeMailService->sendContactEmail(
                'noreply@infpf.fr',
                'elyes@xeilos.fr',
                $subject,
                $emailContent,
                $email  // Reply-To: email du visiteur
            );

            $this->logger->info('Erreur signalée', [
                'error_code' => $errorCode,
                'reporter_email' => $email,
                'reporter_name' => $name,
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès ! Nous vous recontacterons rapidement.'
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Échec envoi email erreur', [
                'error' => $e->getMessage(),
                'reporter_email' => $email,
            ]);

            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer ou nous contacter directement à elyes@xeilos.fr'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function renderEmailContent(
        string $name,
        string $email,
        string $message,
        string $errorCode,
        string $errorUrl
    ): string {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    background: linear-gradient(135deg, #0b3f89 0%, #1a5bb8 100%);
                    color: white;
                    padding: 30px;
                    border-radius: 10px 10px 0 0;
                    text-align: center;
                }
                .content {
                    background: #f9fafb;
                    padding: 30px;
                    border: 1px solid #e5e7eb;
                    border-top: none;
                    border-radius: 0 0 10px 10px;
                }
                .info-box {
                    background: white;
                    padding: 15px;
                    border-radius: 8px;
                    margin: 15px 0;
                    border-left: 4px solid #0b3f89;
                }
                .info-label {
                    font-weight: 600;
                    color: #0b3f89;
                    margin-bottom: 5px;
                }
                .info-value {
                    color: #4b5563;
                }
                .message-box {
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    margin: 15px 0;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 14px;
                    color: #6b7280;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1 style="margin: 0; font-size: 28px;">🔴 Erreur Signalée</h1>
                <p style="margin: 10px 0 0 0; opacity: 0.9;">Code d'erreur : {$errorCode}</p>
            </div>
            
            <div class="content">
                <div class="info-box">
                    <div class="info-label">👤 Nom du visiteur</div>
                    <div class="info-value">{$name}</div>
                </div>
                
                <div class="info-box">
                    <div class="info-label">📧 Email de contact</div>
                    <div class="info-value"><a href="mailto:{$email}" style="color: #0b3f89; text-decoration: none;">{$email}</a></div>
                </div>
                
                <div class="info-box">
                    <div class="info-label">🔗 URL de l'erreur</div>
                    <div class="info-value" style="word-break: break-all;">{$errorUrl}</div>
                </div>
                
                <div class="info-box">
                    <div class="info-label">💬 Message du visiteur</div>
                    <div class="message-box">{$message}</div>
                </div>
                
                <div class="footer">
                    <p>Email envoyé automatiquement depuis infpf.fr</p>
                    <p>Date : {$this->getCurrentDateTime()}</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    private function getCurrentDateTime(): string
    {
        return (new \DateTime())->format('d/m/Y à H:i:s');
    }
}

