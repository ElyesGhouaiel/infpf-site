<?php 
// src/Service/NativeMailService.php
namespace App\Service;

use Psr\Log\LoggerInterface;

class NativeMailService
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sendContactEmail(string $from, string $to, string $subject, string $content): void
    {
        // Configuration des en-têtes
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=utf-8',
            'From: ' . $from,
            'Reply-To: ' . $from,
            'X-Mailer: PHP/' . phpversion(),
            'X-Priority: 1',
            'Importance: High'
        ];
        
        $headersString = implode("\r\n", $headers);
        
        // Enregistrer l'email dans un fichier pour debug
        $this->saveEmailToFile($from, $to, $subject, $content);
        
        // Envoi avec la fonction mail() native PHP
        try {
            $result = mail($to, $subject, $content, $headersString);
            
            if ($result) {
                $this->logger->info('Email sent successfully with native mail()');
                
                // Log supplémentaire
                $logFile = __DIR__ . '/../../var/log/email_native.log';
                file_put_contents(
                    $logFile,
                    date('Y-m-d H:i:s') . " - Email envoyé avec succès via mail() native\n" .
                    "   From: $from\n" .
                    "   To: $to\n" .
                    "   Subject: $subject\n",
                    FILE_APPEND
                );
            } else {
                $this->logger->error('Failed to send email with native mail()');
                throw new \Exception('Échec de l\'envoi d\'email avec mail()');
            }
        } catch (\Exception $e) {
            $this->logger->error('Error sending email: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function saveEmailToFile(string $from, string $to, string $subject, string $content): void
    {
        $emailFile = __DIR__ . '/../../var/log/emails_' . date('Y-m-d') . '.txt';
        $emailContent = "\n" . str_repeat("=", 80) . "\n";
        $emailContent .= "DATE: " . date('Y-m-d H:i:s') . "\n";
        $emailContent .= "FROM: $from\n";
        $emailContent .= "TO: $to\n";
        $emailContent .= "SUBJECT: $subject\n";
        $emailContent .= str_repeat("-", 80) . "\n";
        $emailContent .= $content . "\n";
        $emailContent .= str_repeat("=", 80) . "\n";
        
        file_put_contents($emailFile, $emailContent, FILE_APPEND | LOCK_EX);
    }
}

