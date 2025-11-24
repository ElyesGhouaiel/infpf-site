<?php
// Test direct des headers sans passer par Symfony
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-Test-Manual: TestValue123');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=31536000');

echo "Headers envoyés directement via PHP. Testez avec curl -I";
