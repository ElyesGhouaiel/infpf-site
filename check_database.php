<?php
// Script de vérification de la base de données pour le formulaire de contact
require __DIR__ . '/vendor/autoload.php';

echo "=== VÉRIFICATION BASE DE DONNÉES INFPF ===\n\n";

// Configuration de la base de données (à adapter selon votre configuration)
$dbConfig = [
    'host' => '127.0.0.1',
    'dbname' => 'u665392393_INFPFBDD',
    'username' => 'u665392393_MADV',
    'password' => 'Mbfa2408833'
];

echo "1. Test de connexion à la base de données :\n";

try {
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ Connexion réussie\n";
    echo "   → Base : {$dbConfig['dbname']}\n";
    echo "   → Host : {$dbConfig['host']}\n\n";
    
} catch (Exception $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage() . "\n";
    echo "   → Vérifiez les paramètres de connexion\n";
    exit(1);
}

echo "2. Vérification de la table 'message' :\n";

try {
    // Vérifier si la table existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'message'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'message' existe\n";
        
        // Vérifier la structure de la table
        $stmt = $pdo->query("DESCRIBE message");
        $columns = $stmt->fetchAll();
        
        echo "   → Structure de la table :\n";
        foreach ($columns as $column) {
            echo "     - {$column['Field']} : {$column['Type']} " . 
                 ($column['Null'] === 'NO' ? '(NOT NULL)' : '(NULLABLE)') . 
                 ($column['Key'] ? " [{$column['Key']}]" : '') . "\n";
        }
        
        // Compter les messages
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM message");
        $count = $stmt->fetch()['count'];
        echo "   → Nombre de messages : $count\n";
        
        // Afficher les 5 derniers messages
        if ($count > 0) {
            echo "   → 5 derniers messages :\n";
            $stmt = $pdo->query("SELECT id, name, email, numero, content, id FROM message ORDER BY id DESC LIMIT 5");
            $messages = $stmt->fetchAll();
            
            foreach ($messages as $msg) {
                echo "     ID {$msg['id']} : {$msg['name']} ({$msg['email']}) - " . 
                     substr($msg['content'], 0, 50) . "... - {$msg['id']}\n";
            }
        }
        
    } else {
        echo "❌ Table 'message' n'existe pas\n";
        echo "   → Exécutez : php bin/console doctrine:schema:update --force\n";
        echo "   → Ou : php bin/console doctrine:migrations:migrate\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la vérification : " . $e->getMessage() . "\n";
}

echo "\n3. Test d'insertion d'un message de test :\n";

try {
    $testMessage = [
        'name' => 'Test Automatique',
        'email' => 'test@infpf.fr',
        'numero' => '06 12 34 56 78',
        'content' => 'Ceci est un message de test automatique pour vérifier le fonctionnement du formulaire de contact.',
        'id' => date('Y-m-d H:i:s')
    ];
    
    $sql = "INSERT INTO message (name, email, numero, content, id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $testMessage['name'],
        $testMessage['email'],
        $testMessage['numero'],
        $testMessage['content'],
        $testMessage['id']
    ]);
    
    if ($result) {
        $lastId = $pdo->lastInsertId();
        echo "✅ Message de test inséré avec succès (ID: $lastId)\n";
        
        // Supprimer le message de test
        $pdo->prepare("DELETE FROM message WHERE id = ?")->execute([$lastId]);
        echo "   → Message de test supprimé\n";
    } else {
        echo "❌ Échec de l'insertion du message de test\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test d'insertion : " . $e->getMessage() . "\n";
}

echo "\n4. Vérification des autres tables liées :\n";

$tables = ['user', 'formation', 'category', 'blog', 'comment'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            echo "✅ Table '$table' : $count enregistrements\n";
        } else {
            echo "❌ Table '$table' manquante\n";
        }
    } catch (Exception $e) {
        echo "❌ Erreur table '$table' : " . $e->getMessage() . "\n";
    }
}

echo "\n5. Recommandations :\n";
echo "   🗄️ Si la table 'message' n'existe pas :\n";
echo "      → php bin/console doctrine:schema:update --force\n";
echo "      → Ou : php bin/console doctrine:migrations:migrate\n\n";

echo "   🔧 Si erreur de connexion :\n";
echo "      → Vérifiez les paramètres dans config/packages/doctrine.yaml\n";
echo "      → Vérifiez les variables d'environnement\n\n";

echo "   📊 Pour voir tous les messages :\n";
echo "      → SELECT * FROM message ORDER BY id DESC;\n\n";

echo "=== FIN DE LA VÉRIFICATION ===\n";



