<?php 

// src/Service/MailService.php
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
        } catch (\Exception $e) {
            $this->logger->error('Error sending email: ' . $e->getMessage());
        }
    }
}