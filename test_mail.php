<?php
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

require __DIR__ . '/vendor/autoload.php';

$transport = Transport::fromDsn('smtp://contact@infpf.fr:generated_app_password@smtp.gmail.com:587');
$mailer = new Mailer($transport);

$email = (new Email())
    ->from('contact@infpf.fr')
    ->to('contact@infpf.fr')  // Assurez-vous que cette adresse est correcte
    ->subject('Test Email')
    ->html('<p>Ceci est un test d\'envoi d\'email.</p>');

try {
    $mailer->send($email);
    echo 'Email envoyé avec succès';
} catch (\Exception $e) {
    echo 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage();
}