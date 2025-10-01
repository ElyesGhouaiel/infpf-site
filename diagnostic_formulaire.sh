#!/bin/bash

echo "=== DIAGNOSTIC FORMULAIRE CONTACT INFPF ==="
echo "Date: $(date)"
echo ""

echo "1. Test de la page contact (GET):"
curl -I https://infpf.fr/contactez-nous
echo ""

echo "2. Test du formulaire avec curl (POST):"
curl -X POST https://infpf.fr/contactez-nous \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "contact_form[name]=Test Curl&contact_form[email]=test@curl.com&contact_form[numero]=06 12 34 56 78&contact_form[content]=Test via curl&contact_form[_token]=test" \
  -v
echo ""

echo "3. Vérification des logs:"
tail -20 var/log/prod.log
echo ""

echo "4. Test du contrôleur avec logs:"
php -r "
require 'vendor/autoload.php';
try {
    \$kernel = new \App\Kernel('prod', false);
    \$kernel->boot();
    \$container = \$kernel->getContainer();
    \$logger = \$container->get('logger');
    \$logger->info('Test log direct');
    echo 'Log test envoyé' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Erreur: ' . \$e->getMessage() . PHP_EOL;
}
"
echo ""

echo "5. Vérification du template contact:"
grep -A5 -B5 "form_start\|form_end" templates/content/contact/index.html.twig
echo ""

echo "6. Vérification du contrôleur:"
head -20 src/Controller/ContactController.php
echo ""

echo "7. Vérification de la base de données:"
php check_database.php
echo ""

echo "8. Test d'envoi d'email:"
php test_email_sending.php
echo ""

echo "=== FIN DU DIAGNOSTIC ==="
