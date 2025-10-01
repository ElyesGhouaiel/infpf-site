<?php
// Script de test pour vérifier le formulaire de contact
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

echo "=== TEST DU FORMULAIRE DE CONTACT INFPF ===\n\n";

// 1. Test de la configuration email
echo "1. Vérification de la configuration email...\n";

// Vérifier si le fichier .env existe
if (!file_exists(__DIR__ . '/.env')) {
    echo "❌ Fichier .env manquant\n";
    echo "   → Création d'un fichier .env de base...\n";
    
    $envContent = "# Configuration email pour INFPF
MAILER_DSN=smtp://contact@infpf.fr:VOTRE_MOT_DE_PASSE_APP@smtp.gmail.com:587
# Ou pour un autre fournisseur :
# MAILER_DSN=smtp://username:password@smtp.example.com:587

# Configuration de base
APP_ENV=prod
APP_SECRET=your-secret-key-here
";
    
    file_put_contents(__DIR__ . '/.env', $envContent);
    echo "   ✅ Fichier .env créé\n";
} else {
    echo "✅ Fichier .env trouvé\n";
}

// 2. Test de la base de données
echo "\n2. Vérification de la base de données...\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=u665392393_infpf', 'u665392393_infpf', 'VOTRE_MOT_DE_PASSE_DB');
    echo "✅ Connexion à la base de données réussie\n";
    
    // Vérifier si la table message existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'message'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'message' existe\n";
        
        // Compter les messages existants
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM message");
        $count = $stmt->fetch()['count'];
        echo "   → Nombre de messages en base : $count\n";
    } else {
        echo "❌ Table 'message' manquante\n";
        echo "   → Exécutez : php bin/console doctrine:schema:update --force\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur de connexion à la base : " . $e->getMessage() . "\n";
}

// 3. Test de l'envoi d'email
echo "\n3. Test d'envoi d'email...\n";

// Configuration email de test (à adapter selon votre configuration)
$dsn = 'smtp://contact@infpf.fr:VOTRE_MOT_DE_PASSE_APP@smtp.gmail.com:587';

try {
    $transport = Transport::fromDsn($dsn);
    $mailer = new Mailer($transport);
    
    $email = (new Email())
        ->from('contact@infpf.fr')
        ->to('contact@infpf.fr')
        ->subject('🧪 Test Formulaire Contact INFPF - ' . date('Y-m-d H:i:s'))
        ->html('
        <h2>Test du formulaire de contact INFPF</h2>
        <p><strong>Date du test :</strong> ' . date('Y-m-d H:i:s') . '</p>
        <p><strong>Nom :</strong> Test Automatique</p>
        <p><strong>Email :</strong> test@infpf.fr</p>
        <p><strong>Téléphone :</strong> 06 12 34 56 78</p>
        <p><strong>Message :</strong> Ceci est un test automatique du formulaire de contact.</p>
        <hr>
        <p><em>Si vous recevez cet email, le système de contact fonctionne correctement !</em></p>
        ');
    
    $mailer->send($email);
    echo "✅ Email de test envoyé avec succès\n";
    echo "   → Vérifiez votre boîte email contact@infpf.fr\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de l'envoi de l'email : " . $e->getMessage() . "\n";
    echo "   → Vérifiez la configuration MAILER_DSN dans .env\n";
    echo "   → Vérifiez les identifiants email\n";
}

// 4. Test du formulaire Symfony
echo "\n4. Vérification des fichiers du formulaire...\n";

$files = [
    'src/Controller/ContactController.php' => 'Contrôleur',
    'src/Form/ContactFormType.php' => 'Formulaire',
    'src/Entity/Message.php' => 'Entité',
    'src/Service/MailService.php' => 'Service Email',
    'templates/content/contact/index.html.twig' => 'Template',
    'templates/content/contact/email.html.twig' => 'Template Email'
];

foreach ($files as $file => $description) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ $description : $file\n";
    } else {
        echo "❌ $description manquant : $file\n";
    }
}

// 5. Recommandations
echo "\n5. RECOMMANDATIONS POUR CORRIGER LES PROBLÈMES :\n";
echo "   📧 Configuration email :\n";
echo "      → Éditez le fichier .env et configurez MAILER_DSN\n";
echo "      → Exemple : MAILER_DSN=smtp://user:pass@smtp.gmail.com:587\n";
echo "      → Ou utilisez un service comme Mailgun, SendGrid, etc.\n\n";

echo "   🗄️ Base de données :\n";
echo "      → Exécutez : php bin/console doctrine:schema:update --force\n";
echo "      → Ou : php bin/console doctrine:migrations:migrate\n\n";

echo "   🔧 Cache :\n";
echo "      → Exécutez : php bin/console cache:clear --env=prod\n\n";

echo "   🧪 Test manuel :\n";
echo "      → Allez sur https://infpf.fr/contactez-nous\n";
echo "      → Remplissez le formulaire\n";
echo "      → Vérifiez les logs dans var/log/prod.log\n\n";

echo "=== FIN DU TEST ===\n";



