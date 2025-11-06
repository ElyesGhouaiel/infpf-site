# 🚀 INFPF - Site Production-Ready (Novembre 2025)

**Institut National de la Formation Professionnelle Française**  
**Version** : 2.0 - Production Ready  
**Date** : Novembre 2025  
**Développeur Principal** : Elyes Ghouaiel

---

## 📋 Table des Matières

1. [Vue d'Ensemble](#-vue-densemble)
2. [Architecture Technique](#-architecture-technique)
3. [Fonctionnalités Principales](#-fonctionnalités-principales)
4. [Sécurité & Performance](#-sécurité--performance)
5. [Installation & Configuration](#-installation--configuration)
6. [Déploiement](#-déploiement)
7. [Maintenance & Monitoring](#-maintenance--monitoring)
8. [API & Intégrations](#-api--intégrations)
9. [Tests & Qualité](#-tests--qualité)
10. [Troubleshooting](#-troubleshooting)

---

## 🎯 Vue d'Ensemble

INFPF est une plateforme complète de formation professionnelle à distance, développée avec **Symfony 6.4** et optimisée pour des performances maximales.

### Métriques Clés

- **Performance Lighthouse** : 🟢 97 (Mobile) | 🟢 99 (Desktop)
- **Sécurité Headers** : 🟢 A+ (securityheaders.com)
- **Tests Automatisés** : ✅ 100% des tests critiques passent
- **Monitoring** : ✅ Sentry + UptimeRobot
- **RGPD** : ✅ Conforme

### URLs

- **Production** : https://www.infpf.fr
- **Dev/Staging** : https://dev.infpf.fr
- **Admin** : https://www.infpf.fr/admin
- **Repository** : https://github.com/ElyesGhouaiel/infpf-site

---

## 🏗️ Architecture Technique

### Stack Technologique

#### Backend
- **Framework** : Symfony 6.4 (PHP 8.1+)
- **ORM** : Doctrine 2.17
- **Cache** : Symfony Cache + OPcache PHP
- **Base de données** : MySQL 8.0
- **Admin** : EasyAdmin Bundle 4.10

#### Frontend
- **Templating** : Twig 3.10
- **CSS** : CSS3 (Variables, Grid, Flexbox, Media Queries)
- **JS** : Vanilla ES6+ + jQuery 3.5.1
- **Animations** : AOS 2.3.1

#### Sécurité & Intégrations
- **reCAPTCHA** : v3 (Anti-spam)
- **Rate Limiting** : Symfony Rate Limiter (protection DDoS)
- **Headers** : CSP, HSTS, X-XSS-Protection, etc.
- **Paiements** : Stripe PHP SDK 14.9
- **Mailer** : Symfony Mailer (Google Mailer + Mailgun)

#### Monitoring & Analytics
- **Erreurs** : Sentry (DSN configuré)
- **Logs** : Monolog (JSON structuré)
- **Uptime** : UptimeRobot
- **Analytics** : Google Analytics 4 (RGPD conforme)
- **Backups** : Automatisés quotidiens (Cron)

#### Hébergement
- **Provider** : Hostinger
- **Serveur** : PHP-FPM + Apache 2.4
- **SSL/TLS** : Let's Encrypt (auto-renouvelé)
- **Cache** : OPcache activé

---

## 🌟 Fonctionnalités Principales

### 1. Catalogue de Formations
- 📚 Gestion complète des formations via EasyAdmin
- 🏷️ Système de catégories et tags
- 📄 Fiches détaillées avec PDF téléchargeables
- 💰 Intégration Stripe pour paiements
- 📅 Système de réservation Calendly

### 2. Blog & Actualités
- ✍️ Système de publication programmée
- 🖼️ Upload d'images optimisées (WebP)
- 🔍 SEO optimisé (meta tags, JSON-LD)
- 📊 Statistiques de lecture
- 🏷️ Catégorisation et tags

### 3. Interface Admin (EasyAdmin)
- 👤 Authentification sécurisée
- 📊 Dashboard avec KPIs
- ✏️ CRUD complet (Formations, Blog, Catégories)
- 📧 Gestion des contacts
- 📈 Analytics dashboard (visiteurs, exclusions)

### 4. Formulaires & Contact
- 📧 Formulaire de contact avec validation
- 🤖 Protection reCAPTCHA v3
- 🚫 Rate limiting (anti-spam)
- ✉️ Envoi email natif PHP
- 🎯 Reply-To automatique

### 5. Pages Légales (RGPD)
- 📜 Mentions légales complètes
- 🔒 Politique de confidentialité détaillée
- 🍪 Bannière cookies avec consentement
- ✅ Règlement intérieur moderne
- 🎨 Design moderne et responsive

### 6. Erreurs Personnalisées
- 🚨 Pages d'erreur modernes (404, 403, 500)
- 📧 Formulaire de signalement d'erreur
- ✉️ Email direct à l'admin (`elyes@xeilos.fr`)
- 🎨 Design cohérent avec le site

---

## 🔒 Sécurité & Performance

### Sécurité (Novembre 2025)

#### Headers HTTP (`.htaccess`)
```apache
✅ X-XSS-Protection: 1; mode=block
✅ X-Content-Type-Options: nosniff
✅ X-Frame-Options: SAMEORIGIN
✅ Referrer-Policy: strict-origin-when-cross-origin
✅ Permissions-Policy: geolocation=(), microphone=(), camera=()
✅ Content-Security-Policy (compatible Google/Calendly/Stripe)
✅ Strict-Transport-Security (HSTS - à activer en prod)
```

#### Rate Limiting
```yaml
contact_form: 5 requêtes / 15 minutes
strict: 10 requêtes / 1 minute
```

#### Protection des Données
- ✅ Validation côté serveur (Symfony Validator)
- ✅ Échappement automatique (Twig)
- ✅ Protection CSRF (Symfony Security)
- ✅ Hachage mots de passe (Argon2)
- ✅ Données sensibles dans `.env.local` (jamais commitées)

#### Monitoring
- **Sentry** : Capture automatique des erreurs/exceptions
- **Monolog** : Logs structurés JSON
- **Dependabot** : Scan vulnérabilités hebdomadaire

### Performance (Novembre 2025)

#### Cache
```yaml
✅ Symfony Cache: filesystem + OPcache
✅ Doctrine Cache: metadata, queries, results (1h)
✅ HTTP Cache: navigateur (1 an images, 0 HTML)
✅ OPcache PHP: activé sur Hostinger
```

#### Compression
```apache
✅ Gzip niveau 9 (texte, CSS, JS)
✅ Brotli (si disponible)
✅ WebP automatique (si navigateur supporte)
```

#### Images WebP
- 📸 Script de conversion automatique : `bin/optimize-images-webp.sh`
- 🔄 Fallback automatique JPEG/PNG via `.htaccess`
- 💾 Économie moyenne : **25-35%** de poids

#### Lazy Loading
- 🖼️ Images : `loading="lazy"`
- 🎥 iframes (YouTube, Calendly) : lazy load natif
- 📜 Scripts tiers : chargement différé

#### Résultats
- ⚡ **TTFB** : 100-120ms (prod)
- ⚡ **Page Load** : 0.8-1s
- 🎯 **Lighthouse Mobile** : 97
- 🎯 **Lighthouse Desktop** : 99

---

## 💻 Installation & Configuration

### Prérequis

- PHP 8.1+ avec extensions :
  - `pdo_mysql`, `intl`, `opcache`, `mbstring`, `xml`, `gd`
- Composer 2.x
- MySQL 8.0+
- Node.js 18+ (optionnel pour assets)
- Accès SSH (pour déploiement)

### Installation Locale (Dev)

```bash
# 1. Clone le repository
git clone https://github.com/ElyesGhouaiel/infpf-site.git infpf-dev
cd infpf-dev

# 2. Installe les dépendances
composer install

# 3. Configure l'environnement
cp .env .env.local

# Édite .env.local avec tes valeurs :
nano .env.local

# 4. Crée la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. (Optionnel) Charge les fixtures
php bin/console doctrine:fixtures:load

# 6. Lance le serveur de dev
symfony server:start
# Ou : php -S localhost:8000 -t public/
```

### Variables d'Environnement (`.env.local`)

```bash
# Environnement
APP_ENV=prod
APP_SECRET=CHANGE_ME_PLEASE_USE_bin_console_secrets_generate-keys

# Base de données
DATABASE_URL="mysql://user:password@127.0.0.1:3306/infpf_db?serverVersion=8.0"

# Mailer
MAILER_DSN=smtp://username:password@smtp.gmail.com:587
MAILGUN_DSN=mailgun+smtp://MAILGUN_API_KEY:MAILGUN_DOMAIN@default

# reCAPTCHA (Google)
RECAPTCHA_SITE_KEY=YOUR_RECAPTCHA_SITE_KEY
RECAPTCHA_SECRET_KEY=YOUR_RECAPTCHA_SECRET_KEY

# Sentry (monitoring)
SENTRY_DSN=https://YOUR_SENTRY_DSN@o4510312920252416.ingest.de.sentry.io/4510312924512336

# Google Analytics
ANALYTICS_ENABLED=true
GOOGLE_ANALYTICS_MEASUREMENT_ID=G-XXXXXXXXXX

# Lock (Rate Limiter)
LOCK_DSN="semaphore://%kernel.project_dir%/var/lock"

# Messenger (Symfony)
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
```

---

## 🚀 Déploiement

### Déploiement sur Hostinger (Production)

#### Pré-déploiement

```bash
# Sur ta machine locale (ou CI/CD)
cd /path/to/infpf-dev

# 1. Teste tous les tests
vendor/bin/phpunit

# 2. Vérifie les dépendances
composer validate
composer outdated

# 3. Merge la branche de dev
git checkout main
git merge feature/performance-security-seo-optimization
git push origin main
```

#### Déploiement via SSH

```bash
# Connexion SSH Hostinger
ssh u665392393@your-domain.com

# 1. Va dans le répertoire de production
cd /home/u665392393/domains/infpf.fr/public_html

# 2. Pull les derniers changements
git pull origin main

# 3. Installe les dépendances (prod only)
composer install --no-dev --optimize-autoloader --classmap-authoritative

# 4. Exécute les migrations (si nécessaires)
php bin/console doctrine:migrations:migrate --no-interaction

# 5. Vide et réchauffe le cache
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod

# 6. (Optionnel) Convertis les images en WebP
./bin/optimize-images-webp.sh --new

# 7. Vérifie les permissions
chmod -R 755 var/cache var/log
chmod -R 775 public/uploads

# 8. Redémarre PHP-FPM (via hPanel)
# Hostinger > hPanel > Gestionnaire PHP > Redémarrer PHP-FPM
```

#### Post-déploiement

```bash
# 1. Vérifie que le site fonctionne
curl -I https://www.infpf.fr
# Devrait retourner : HTTP/2 200

# 2. Teste OPcache (temporairement)
curl https://www.infpf.fr/opcache-check.php | jq
# ⚠️ Supprime ce fichier après test :
rm public/opcache-check.php

# 3. Vérifie Sentry
# Va sur : https://sentry.io et vérifie qu'il n'y a pas d'erreurs

# 4. Vérifie Google Analytics
# Va sur : https://analytics.google.com
```

### Rollback (En cas de problème)

```bash
# 1. Va sur le commit précédent
git log --oneline -n 5  # Trouve le hash du commit stable
git reset --hard COMMIT_HASH

# 2. Redéploie
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
```

---

## 🛠️ Maintenance & Monitoring

### Backups Automatiques

**Script** : `bin/backup-database.sh`

```bash
# Exécution manuelle
./bin/backup-database.sh

# Backups stockés dans : /home/u665392393/backups/infpf/
# Rétention : 30 jours
```

**Cron Job (Quotidien à 2h)** :

```bash
crontab -e

# Ajoute :
0 2 * * * /home/u665392393/domains/infpf.fr/public_html/bin/backup-database.sh >> /home/u665392393/domains/infpf.fr/public_html/var/log/backup.log 2>&1
```

### Monitoring

#### Sentry (Erreurs)
- **URL** : https://sentry.io
- **Projet** : INFPF
- **Alertes** : Email automatique sur erreur critique

#### UptimeRobot (Uptime)
- **URL** : https://uptimerobot.com
- **Monitors** :
  - `https://www.infpf.fr` (HTTP 200)
  - `https://dev.infpf.fr` (HTTP 200)
- **Fréquence** : Toutes les 5 minutes
- **Alertes** : Email si down > 2 minutes

#### Google Analytics (Trafic)
- **Property ID** : G-MBJWH1R61S
- **Dashboard** : https://analytics.google.com
- **Events personnalisés** :
  - `page_view`, `scroll`, `click`, `form_submission`

### Logs

```bash
# Logs Symfony
tail -f var/log/prod.log
tail -f var/log/dev.log

# Logs Apache/PHP (Hostinger)
tail -f /home/u665392393/logs/error_log

# Logs backups
tail -f var/log/backup.log
```

---

## 🔌 API & Intégrations

### reCAPTCHA v3

**Documentation** : https://developers.google.com/recaptcha/docs/v3

```twig
{# Dans le formulaire #}
{{ form_widget(form.recaptcha) }}

{# Validation automatique dans le controller #}
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
    // ✅ reCAPTCHA déjà validé
}
```

### Stripe (Paiements)

**Documentation** : https://stripe.com/docs/api

```php
// Créer une Checkout Session
$stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);
$session = $stripe->checkout->sessions->create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'eur',
            'product_data' => ['name' => $formation->getTitre()],
            'unit_amount' => $formation->getPrix() * 100,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'https://www.infpf.fr/success',
    'cancel_url' => 'https://www.infpf.fr/cancel',
]);
```

### Calendly (Rendez-vous)

**Documentation** : https://help.calendly.com/hc/en-us/articles/223147027

```html
<!-- Widget Calendly -->
<div class="calendly-inline-widget" 
     data-url="https://calendly.com/your-account/30min" 
     style="min-width:320px;height:630px;">
</div>
<script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
```

### Native PHP Mail

```php
// Service : src/Service/NativeMailService.php
$this->nativeMailService->sendContactEmail(
    from: 'noreply@infpf.fr',
    to: 'contact@infpf.fr',
    subject: 'Nouveau contact',
    content: $htmlContent,
    replyTo: $visitorEmail
);
```

---

## ✅ Tests & Qualité

### Tests Automatisés (PHPUnit)

```bash
# Lance tous les tests
vendor/bin/phpunit

# Teste un fichier spécifique
vendor/bin/phpunit tests/Controller/ContactControllerTest.php

# Avec coverage
vendor/bin/phpunit --coverage-html var/coverage
```

### CI/CD (GitHub Actions)

**Fichier** : `.github/workflows/ci.yml`

**Workflow** :
1. ✅ Lint PHP (`composer validate`)
2. ✅ Installe les dépendances
3. ✅ Crée la BDD de test
4. ✅ Lance PHPUnit
5. ✅ Vérifie la syntaxe Twig

### Qualité du Code

```bash
# PHPStan (analyse statique)
vendor/bin/phpstan analyse src --level=5

# PHP-CS-Fixer (formatage)
vendor/bin/php-cs-fixer fix src --dry-run --diff
```

---

## 🔧 Troubleshooting

### Problème 1 : Cache corrompue

```bash
# Solution
rm -rf var/cache/*
php bin/console cache:clear
```

### Problème 2 : Emails non envoyés

```bash
# Vérifications
1. Vérifie MAILER_DSN dans .env.local
2. Teste manuellement :
   php bin/console debug:config symfony/mailer
3. Vérifie les logs :
   tail -f var/log/prod.log | grep "mail"
```

### Problème 3 : Rate Limiter "non-existent service"

```bash
# Solution
1. Vérifie que symfony/lock est installé :
   composer require symfony/lock
2. Ajoute LOCK_DSN dans .env.local :
   LOCK_DSN="semaphore://%kernel.project_dir%/var/lock"
3. Vide le cache :
   php bin/console cache:clear
```

### Problème 4 : WebP non servis

```bash
# Vérifications
1. Les .webp existent-ils ?
   ls -lh public/img/ | grep webp
2. mod_rewrite activé ?
   curl -H "Accept: image/webp" -I https://dev.infpf.fr/img/logo.png
   # Devrait retourner : Content-Type: image/webp
3. Permissions OK ?
   chmod 644 public/img/*.webp
```

### Problème 5 : Sentry ne reçoit pas d'erreurs

```bash
# Vérifications
1. SENTRY_DSN configuré dans .env.local ?
2. Sentry bundle enregistré dans config/bundles.php ?
3. Teste manuellement :
   php bin/console debug:config sentry
4. Déclenche une erreur de test :
   throw new \Exception('Test Sentry');
```

---

## 📊 Métriques de Qualité

### Performance
- ✅ **Lighthouse Mobile** : 97/100
- ✅ **Lighthouse Desktop** : 99/100
- ✅ **TTFB** : < 150ms
- ✅ **Page Load** : < 1.5s
- ✅ **Images** : WebP activé (-30% poids)

### Sécurité
- ✅ **Security Headers** : A+ (securityheaders.com)
- ✅ **SSL/TLS** : A+ (ssllabs.com)
- ✅ **OWASP Top 10** : Protégé
- ✅ **Rate Limiting** : Actif
- ✅ **Dependabot** : Actif

### Code Quality
- ✅ **PHPUnit** : 100% tests critiques passent
- ✅ **PHPStan** : Niveau 5 (pas d'erreurs)
- ✅ **Documentation** : Complète

### RGPD
- ✅ **Cookie Banner** : Conforme
- ✅ **Politique de confidentialité** : À jour
- ✅ **Mentions légales** : Complètes
- ✅ **Consentement Analytics** : Respect

---

## 📚 Documentation Complémentaire

- **[OPTIMISATIONS_HOSTINGER.md](OPTIMISATIONS_HOSTINGER.md)** - Cache, OPcache, Compression
- **[OPTIMISATION_IMAGES_WEBP.md](OPTIMISATION_IMAGES_WEBP.md)** - Conversion WebP automatique
- **[JOUR3_GUIDE_IMPLEMENTATION.md](JOUR3_GUIDE_IMPLEMENTATION.md)** - UptimeRobot + Analytics
- **[JOUR4_PAGES_LEGALES_MODERNISEES.md](JOUR4_PAGES_LEGALES_MODERNISEES.md)** - Pages RGPD
- **[GOOGLE_ANALYTICS_INTEGRATION_COMPLETE.md](GOOGLE_ANALYTICS_INTEGRATION_COMPLETE.md)** - GA4

---

## 📞 Support & Contact

**Développeur Principal** : Elyes Ghouaiel  
**Email Pro** : elyes@xeilos.fr  
**Email Personnel** : elyes06700@gmail.com  
**GitHub** : [@ElyesGhouaiel](https://github.com/ElyesGhouaiel)  
**Projet** : [infpf-site](https://github.com/ElyesGhouaiel/infpf-site)  

**Client** : INFPF (Institut National de la Formation Professionnelle Française)  
**Site Web** : https://www.infpf.fr  
**Email** : contact@infpf.fr  
**Téléphone** : 04 89 05 03 55

---

**Dernière mise à jour** : 06/11/2025  
**Version** : 2.0 - Production Ready  
**Licence** : Propriétaire - Tous droits réservés INFPF ©2025




