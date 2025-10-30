<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trouver mon IP publique - INFPF</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            color: #1e293b;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #0b3f89;
            margin-bottom: 10px;
        }
        .ip-display {
            background: linear-gradient(135deg, #0b3f89 0%, #1e5cb8 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            font-size: 32px;
            font-weight: 700;
            text-align: center;
            margin: 30px 0;
            word-break: break-all;
        }
        .info {
            background: #dbeafe;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #0b3f89;
        }
        .warning {
            background: #fef3c7;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #f59e0b;
        }
        .code-block {
            background: #1e293b;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
        }
        button {
            background: #0b3f89;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            display: block;
            width: 100%;
            margin: 10px 0;
        }
        button:hover {
            background: #0a3370;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
            display: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🌐 Votre IP publique</h1>
        <p>Cette page vous aide à trouver votre adresse IP publique pour l'exclure du système d'analytics.</p>

        <?php
        // Récupérer l'IP réelle du client
        function getClientIP() {
            $headers = [
                'HTTP_CF_CONNECTING_IP',  // Cloudflare
                'HTTP_X_REAL_IP',         // Nginx proxy
                'HTTP_X_FORWARDED_FOR',   // Standard proxy
                'REMOTE_ADDR',            // IP directe
            ];

            foreach ($headers as $header) {
                if (!empty($_SERVER[$header])) {
                    $ip = $_SERVER[$header];
                    // X-Forwarded-For peut contenir plusieurs IPs
                    if (strpos($ip, ',') !== false) {
                        $ips = array_map('trim', explode(',', $ip));
                        $ip = $ips[0]; // Prendre la première (IP cliente)
                    }
                    
                    // Vérifier que l'IP est valide et publique
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            }

            // Fallback : IP directe (même si privée)
            return $_SERVER['REMOTE_ADDR'] ?? 'Inconnue';
        }

        $clientIP = getClientIP();
        $isPrivateIP = !filter_var($clientIP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        ?>

        <div class="ip-display" id="ipDisplay">
            <?php echo htmlspecialchars($clientIP); ?>
        </div>

        <button onclick="copyIP()">📋 Copier l'IP</button>
        <div class="success" id="copySuccess">✅ IP copiée dans le presse-papiers !</div>

        <?php if ($isPrivateIP): ?>
        <div class="warning">
            <strong>⚠️ Attention !</strong><br>
            L'IP détectée (<code><?php echo htmlspecialchars($clientIP); ?></code>) est une <strong>IP privée/locale</strong>.<br><br>
            
            <strong>Pourquoi ?</strong><br>
            - Vous êtes peut-être en développement local (localhost, 127.0.0.1)<br>
            - Vous êtes sur un réseau local (192.168.x.x, 10.x.x.x)<br>
            - Le serveur ne voit pas votre IP publique<br><br>
            
            <strong>Solution :</strong><br>
            Accédez à cette page depuis le domaine de production pour voir votre vraie IP publique.
        </div>
        <?php else: ?>
        <div class="info">
            <strong>✅ IP publique détectée !</strong><br>
            Cette IP peut être utilisée pour l'exclusion du tracking.
        </div>
        <?php endif; ?>

        <div class="info">
            <strong>📝 Comment exclure cette IP ?</strong><br><br>
            
            1. Éditez le fichier <code>config/analytics_config.php</code><br><br>
            
            2. Ajoutez votre IP dans la section <code>excluded_public_ips</code> :<br>
            <div class="code-block">'excluded_public_ips' => [
    '<?php echo htmlspecialchars($clientIP); ?>',  // Votre IP
    // Ajoutez d'autres IPs si nécessaire
],</div>
            
            3. Sauvegardez et rechargez la page<br><br>
            
            4. Testez sur : <a href="/TEST_EXCLUSIONS.html" style="color: #0b3f89; font-weight: 600;">/TEST_EXCLUSIONS.html</a>
        </div>

        <div class="warning">
            <strong>⚠️ Important :</strong><br>
            - Les IPs publiques peuvent changer (DHCP dynamique)<br>
            - Si votre IP change, vous devrez mettre à jour la configuration<br>
            - Pour un blocage permanent, utilisez plutôt le <strong>cookie développeur</strong> :<br>
            <div class="code-block">// Dans la console navigateur :
window.INFPFAnalytics.setDevCookie();</div>
        </div>

        <h2>📊 Informations supplémentaires</h2>
        <div class="info" style="background: #f1f5f9; font-size: 14px;">
            <strong>User-Agent:</strong><br>
            <code><?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Inconnu'); ?></code><br><br>
            
            <strong>Langue:</strong>
            <code><?php echo htmlspecialchars($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'Inconnue'); ?></code><br><br>
            
            <strong>Référent:</strong>
            <code><?php echo htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'Aucun'); ?></code>
        </div>

        <div class="info">
            <strong>🔗 Liens utiles :</strong><br>
            • <a href="/TEST_EXCLUSIONS.html" style="color: #0b3f89;">Page de test des exclusions</a><br>
            • <a href="/config/ANALYTICS_SETUP.md" style="color: #0b3f89;">Documentation complète</a><br>
            • <a href="/admin/analytics" style="color: #0b3f89;">Dashboard Analytics</a>
        </div>
    </div>

    <script>
        function copyIP() {
            const ip = document.getElementById('ipDisplay').textContent.trim();
            navigator.clipboard.writeText(ip).then(() => {
                const success = document.getElementById('copySuccess');
                success.style.display = 'block';
                setTimeout(() => {
                    success.style.display = 'none';
                }, 3000);
            });
        }
    </script>
</body>
</html>

