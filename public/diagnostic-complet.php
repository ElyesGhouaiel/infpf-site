<?php
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>🔧 Diagnostic Complet - <?php echo date('H:i:s'); ?></title>
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        padding: 30px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
        background: #f8f9fa;
        line-height: 1.6;
    }
    .container { max-width: 1200px; margin: 0 auto; }
    h1 { color: #0b3f89; margin-bottom: 20px; }
    .info-box {
        background: white;
        padding: 20px;
        margin: 20px 0;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .success { border-left: 4px solid #00CC66; }
    .error { border-left: 4px solid #FF3333; }
    .warning { border-left: 4px solid #FF9900; }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }
    th {
        background: #f5f5f5;
        font-weight: 600;
    }
    .test-svg {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        background: linear-gradient(180deg, #1E5AE6, #0E44BF);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    .test-svg svg {
        width: 30px;
        height: 30px;
    }
    code {
        background: #f5f5f5;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
    }
    .btn {
        display: inline-block;
        padding: 10px 20px;
        background: #0b3f89;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        margin: 5px;
    }
    .btn:hover { background: #0a3470; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 DIAGNOSTIC COMPLET DU PROBLÈME</h1>
        <p><strong>Timestamp:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        
        <!-- 1. Tests PHP/Serveur -->
        <div class="info-box <?php echo function_exists('opcache_get_status') ? 'success' : 'warning'; ?>">
            <h2>1️⃣ Configuration Serveur</h2>
            <table>
                <tr>
                    <th>Test</th>
                    <th>Résultat</th>
                    <th>Statut</th>
                </tr>
                <tr>
                    <td>OPcache</td>
                    <td><?php echo function_exists('opcache_get_status') ? (opcache_get_status()['opcache_enabled'] ? '✅ Activé' : '⚠️ Désactivé') : '❌ Non disponible'; ?></td>
                    <td><?php echo function_exists('opcache_get_status') && opcache_get_status()['opcache_enabled'] ? 'OK' : 'Attention'; ?></td>
                </tr>
                <tr>
                    <td>APCu</td>
                    <td><?php echo function_exists('apcu_cache_info') ? '✅ Activé' : '❌ Non disponible'; ?></td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Template show.html.twig</td>
                    <td><?php 
                        $templatePath = __DIR__ . '/../templates/content/formation/show.html.twig';
                        echo file_exists($templatePath) ? '✅ Existe (' . date('Y-m-d H:i:s', filemtime($templatePath)) . ')' : '❌ Introuvable';
                    ?></td>
                    <td><?php echo file_exists($templatePath) ? 'OK' : 'ERREUR'; ?></td>
                </tr>
                <tr>
                    <td>Cache Symfony</td>
                    <td><?php 
                        $cacheDir = __DIR__ . '/../var/cache/prod';
                        echo is_dir($cacheDir) ? '✅ Existe (' . count(scandir($cacheDir)) . ' fichiers)' : '❌ Vide';
                    ?></td>
                    <td>-</td>
                </tr>
            </table>
        </div>
        
        <!-- 2. Test SVG dans HTML -->
        <div class="info-box success">
            <h2>2️⃣ Test SVG Directs (sans cache)</h2>
            <p>Si tu vois les formes colorées, les SVG fonctionnent :</p>
            <div style="display: flex; gap: 20px; margin: 20px 0; flex-wrap: wrap;">
                <div>
                    <div class="test-svg">
                        <svg viewBox="0 0 24 24">
                            <rect x="6" y="6" width="12" height="12" fill="#00CC66"/>
                        </svg>
                    </div>
                    <p><strong>Carré VERT</strong></p>
                </div>
                <div>
                    <div class="test-svg">
                        <svg viewBox="0 0 24 24">
                            <polygon points="12,4 22,20 2,20" fill="#0099FF"/>
                        </svg>
                    </div>
                    <p><strong>Triangle BLEU</strong></p>
                </div>
                <div>
                    <div class="test-svg">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="8" fill="#FF6B00"/>
                        </svg>
                    </div>
                    <p><strong>Cercle ORANGE</strong></p>
                </div>
            </div>
        </div>
        
        <!-- 3. Vérification contenu fichier -->
        <div class="info-box <?php
            $templatePath = __DIR__ . '/../templates/content/formation/show.html.twig';
            $hasNewSVG = false;
            if (file_exists($templatePath)) {
                $content = file_get_contents($templatePath);
                $hasNewSVG = strpos($content, 'VERSION-TEST-SVG-SIMPLE') !== false;
            }
            echo $hasNewSVG ? 'success' : 'error';
        ?>">
            <h2>3️⃣ Contenu du Fichier Template</h2>
            <?php if (file_exists($templatePath)): ?>
                <?php if ($hasNewSVG): ?>
                    <p>✅ <strong>Le nouveau SVG simple est présent dans le fichier !</strong></p>
                    <p>Commentaire trouvé : <code>VERSION-TEST-SVG-SIMPLE: 2025-11-12-16h35</code></p>
                <?php else: ?>
                    <p>❌ <strong>L'ancien SVG est encore dans le fichier !</strong></p>
                    <p>Le fichier n'a pas été mis à jour correctement.</p>
                <?php endif; ?>
                <p>Dernière modification : <?php echo date('Y-m-d H:i:s', filemtime($templatePath)); ?></p>
            <?php else: ?>
                <p>❌ Le fichier template n'existe pas !</p>
            <?php endif; ?>
        </div>
        
        <!-- 4. Actions recommandées -->
        <div class="info-box warning">
            <h2>4️⃣ Actions à Faire</h2>
            <ol>
                <li><strong>Vide le cache CDN Hostinger</strong> (Panneau Hostinger → Performance → CDN Cache)</li>
                <li><strong>Attends 2-3 minutes</strong> (le CDN met du temps à se purger)</li>
                <li><strong>Teste en navigation privée</strong> (Ctrl+Shift+N)</li>
                <li><strong>Vérifie la page formation</strong> : <a href="/formation/89" target="_blank">/formation/89</a></li>
            </ol>
        </div>
        
        <!-- 5. Liens de test -->
        <div class="info-box">
            <h2>5️⃣ Pages de Test</h2>
            <a href="/test-svg-simples.html" class="btn" target="_blank">Test SVG Simples</a>
            <a href="/formation/89" class="btn" target="_blank">Formation #89</a>
            <a href="/" class="btn" target="_blank">Accueil</a>
        </div>
        
        <!-- 6. Diagnostic Cache -->
        <div class="info-box">
            <h2>6️⃣ Test Cache Navigateur</h2>
            <p><strong>URL actuelle :</strong> <code><?php echo $_SERVER['REQUEST_URI']; ?></code></p>
            <p><strong>User Agent :</strong> <code><?php echo $_SERVER['HTTP_USER_AGENT']; ?></code></p>
            <p><strong>Headers :</strong></p>
            <ul>
                <li>Cache-Control: <?php echo isset($_SERVER['HTTP_CACHE_CONTROL']) ? $_SERVER['HTTP_CACHE_CONTROL'] : 'Non défini'; ?></li>
                <li>Pragma: <?php echo isset($_SERVER['HTTP_PRAGMA']) ? $_SERVER['HTTP_PRAGMA'] : 'Non défini'; ?></li>
            </ul>
        </div>
        
        <div class="info-box success">
            <h2>✅ Conclusion</h2>
            <p><strong>Si tu vois les 3 formes colorées ci-dessus :</strong> Le problème est le cache CDN/navigateur, PAS le code !</p>
            <p><strong>Si tu ne vois PAS les formes :</strong> Le problème est dans le CSS ou le chargement des ressources.</p>
        </div>
    </div>
</body>
</html>
