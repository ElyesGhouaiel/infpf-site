<?php
require 'vendor/autoload.php';

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

echo "Test formulaire simple...\n";

// Test email direct
try {
    $transport = Transport::fromDsn('smtp://contact@infpf.fr:mjjaswabijrzqxbi@smtp.gmail.com:587');
    $mailer = new Mailer($transport);
    
    $email = (new Email())
        ->from('contact@infpf.fr')
        ->to('contact@infpf.fr')
        ->subject('Test Formulaire Simple - ' . date('H:i:s'))
        ->html('<h2>Test Formulaire Simple</h2><p>Nom: Test Simple</p><p>Email: test@example.com</p><p>Message: Test du formulaire</p>');
    
    $mailer->send($email);
    echo "SUCCÈS: Email envoyé!\n";
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}

// Test base de données
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=u665392393_INFPFBDD', 'u665392393_MADV', 'Mbfa2408833');
    $stmt = $pdo->prepare("INSERT INTO message (name, email, numero, content) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute(['Test Simple', 'test@example.com', '06 12 34 56 78', 'Test du formulaire simple']);
    
    if ($result) {
        $id = $pdo->lastInsertId();
        echo "SUCCÈS: Message sauvegardé en BDD avec ID: $id\n";
    }
} catch (Exception $e) {
    echo "ERREUR BDD: " . $e->getMessage() . "\n";
}
