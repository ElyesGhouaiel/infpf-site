<?php 
// src/Service/MailService.php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class MailService
{
    private $mailer;
    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function sendContactEmail(string $from, string $to, string $subject, string $content): void
    {
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->html($content);

        try {
            $this->mailer->send($email);
            $this->logger->info('Email sent successfully.');
            
            // Enregistrer l'email dans un fichier pour debug
            $this->saveEmailToFile($from, $to, $subject, $content);
            
        } catch (\Exception $e) {
            $this->logger->error('Error sending email: ' . $e->getMessage());
            // Re-lancer l'exception pour que le contrôleur puisse la capturer
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