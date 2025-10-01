<?php
// Script de test spécifique pour l'envoi d'emails
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

echo "=== TEST D'ENVOI D'EMAIL INFPF ===\n\n";

// Configuration de test - À ADAPTER SELON VOTRE CONFIGURATION
$emailConfigs = [
    'gmail' => 'smtp://contact@infpf.fr:VOTRE_MOT_DE_PASSE_APP@smtp.gmail.com:587',
    'gmail_oauth' => 'gmail://contact@infpf.fr:VOTRE_TOKEN_OAUTH@default',
    'mailgun' => 'mailgun://VOTRE_API_KEY@default',
    'sendgrid' => 'sendgrid://VOTRE_API_KEY@default',
    'smtp_local' => 'smtp://localhost:1025',
    'null' => 'null://null'
];

echo "1. Test avec différentes configurations email :\n\n";

foreach ($emailConfigs as $name => $dsn) {
    echo "Test avec $name...\n";
    
    try {
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);
        
        $email = (new Email())
            ->from('contact@infpf.fr')
            ->to('contact@infpf.fr')
            ->subject('Test INFPF - ' . $name . ' - ' . date('H:i:s'))
            ->html('
            <h2>Test Email INFPF - ' . $name . '</h2>
            <p><strong>Configuration :</strong> ' . $name . '</p>
            <p><strong>Heure :</strong> ' . date('Y-m-d H:i:s') . '</p>
            <p>Si vous recevez cet email, la configuration ' . $name . ' fonctionne !</p>
            ');
        
        $mailer->send($email);
        echo "   ✅ Succès avec $name\n";
        
    } catch (Exception $e) {
        echo "   ❌ Échec avec $name : " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "2. Test du template email du formulaire de contact :\n\n";

// Simuler les données du formulaire
$formData = [
    'name' => 'Jean Dupont',
    'email' => 'jean.dupont@example.com',
    'phone' => '06 12 34 56 78',
    'content' => 'Bonjour, je souhaite obtenir des informations sur vos formations en IA. Pouvez-vous me recontacter ?'
];

// Générer le contenu HTML comme dans le template
$emailContent = "
<h1>Nouveau message de contact</h1>
<p><strong>Nom :</strong> " . htmlspecialchars($formData['name']) . "</p>
<p><strong>Email :</strong> " . htmlspecialchars($formData['email']) . "</p>
<p><strong>Téléphone :</strong> " . htmlspecialchars($formData['phone']) . "</p>
<p><strong>Message :</strong> " . nl2br(htmlspecialchars($formData['content'])) . "</p>
<hr>
<p><em>Message reçu le " . date('d/m/Y à H:i:s') . " via le formulaire de contact INFPF</em></p>
";

echo "Contenu généré :\n";
echo "================\n";
echo $emailContent;
echo "\n================\n\n";

echo "3. Test d'envoi avec le contenu du formulaire :\n\n";

try {
    // Utiliser la configuration qui a fonctionné (ou null pour test local)
    $transport = Transport::fromDsn('null://null');
    $mailer = new Mailer($transport);
    
    $email = (new Email())
        ->from('contact@infpf.fr')
        ->to('contact@infpf.fr')
        ->subject('Nouveau message de contact - ' . $formData['name'])
        ->html($emailContent);
    
    $mailer->send($email);
    echo "✅ Email avec contenu du formulaire envoyé (mode null - simulation)\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n4. Vérification des logs :\n";
$logFile = __DIR__ . '/var/log/prod.log';
if (file_exists($logFile)) {
    echo "✅ Fichier de log trouvé : $logFile\n";
    echo "   → Dernières lignes :\n";
    $lines = file($logFile);
    $lastLines = array_slice($lines, -10);
    foreach ($lastLines as $line) {
        echo "     " . trim($line) . "\n";
    }
} else {
    echo "❌ Fichier de log non trouvé : $logFile\n";
}

echo "\n=== FIN DU TEST EMAIL ===\n";



