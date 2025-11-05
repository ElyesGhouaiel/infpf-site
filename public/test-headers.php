<?php
// Test simple pour vérifier si les headers personnalisés passent par le CDN Hostinger

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('X-Test-Header: TestValue123');

echo "Headers envoyés. Vérifiez avec: curl -I https://dev.infpf.fr/test-headers.php";












