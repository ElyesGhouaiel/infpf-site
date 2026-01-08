# Guide de Deploiement INFPF - Production

Ce guide detaille etape par etape le deploiement du site INFPF sur Hostinger en production.

---

## Checklist Pre-Deploiement

Avant de deployer sur `infpf.fr` (production), verifier que :

### Tests et Validation

- [ ] Tous les tests PHPUnit passent (`vendor/bin/phpunit`)
- [ ] Le site fonctionne sur `dev.infpf.fr` sans erreur
- [ ] Lighthouse score >= 95 (Mobile) et >= 97 (Desktop)
- [ ] Tous les formulaires fonctionnent (contact, inscription, etc.)
- [ ] reCAPTCHA v3 fonctionne
- [ ] Rate limiting teste (anti-spam)
- [ ] Pages d'erreur personnalisees testees (404, 403, 500)

### Configuration

- [ ] `.env.local` cree sur production avec les bonnes valeurs
- [ ] SENTRY_DSN configure (monitoring)
- [ ] GOOGLE_ANALYTICS_MEASUREMENT_ID configure
- [ ] MAILER_DSN configure (Gmail ou Mailgun)
- [ ] RECAPTCHA_SITE_KEY / RECAPTCHA_SECRET_KEY configures
- [ ] DATABASE_URL configure (MySQL production)
- [ ] APP_ENV=prod et APP_DEBUG=false

### Securite

- [ ] HSTS active dans `.htaccess` (apres test)
- [ ] Fichiers sensibles dans `.gitignore` (`.env.local`, `var/`)
- [ ] Permissions correctes (`755` pour `var/`, `775` pour `uploads/`)
- [ ] Fichiers de test supprimes du `public/`

### Backups

- [ ] Backup de la base de donnees actuelle
- [ ] Backup des fichiers actuels (`public/uploads/`)
- [ ] Point de restauration cree

---

## Etape 1 : Preparation Locale

### 1.1 Tester en Local / Dev

```bash
cd /home/u665392393/domains/infpf.fr/dev

# 1. Lancer tous les tests
vendor/bin/phpunit

# 2. Verifier les dependances
composer validate
composer outdated

# 3. Verifier la syntaxe YAML
php bin/console lint:yaml config/

# 4. Verifier les routes
php bin/console debug:router

# 5. Vider et rechauffer le cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

### 1.2 Merge la Branche de Dev

```bash
# Si sur une branche feature
git checkout main
git merge dev

# Resoudre les conflits si necessaire
git status

# Commit et push
git push origin main
```

---

## Etape 2 : Backup Avant Deploiement

### 2.1 Backup Base de Donnees

```bash
mysqldump -u USERNAME -p DATABASE_NAME > /home/u665392393/backups/infpf/pre-deploy-$(date +%Y%m%d-%H%M%S).sql
```

### 2.2 Backup Fichiers

```bash
# Sauvegarder les uploads
tar -czf /home/u665392393/backups/infpf/uploads-backup-$(date +%Y%m%d).tar.gz \
    /home/u665392393/domains/infpf.fr/public_html/public/uploads/

# Sauvegarder .env.local
cp /home/u665392393/domains/infpf.fr/public_html/.env.local \
   /home/u665392393/backups/infpf/.env.local.backup
```

---

## Etape 3 : Deploiement sur Production

### 3.1 Pull les Changements

```bash
cd /home/u665392393/domains/infpf.fr/public_html

# 1. Verifier la branche actuelle
git branch

# 2. Pull les derniers changements
git pull origin main

# Si erreur "uncommitted changes", stash-les :
git stash
git pull origin main
git stash pop
```

### 3.2 Installer les Dependances (Prod Only)

```bash
composer install --no-dev --optimize-autoloader --classmap-authoritative
```

### 3.3 Migrations Base de Donnees

```bash
# Verifier les migrations en attente
php bin/console doctrine:migrations:status

# Executer les migrations (mode non-interactif)
php bin/console doctrine:migrations:migrate --no-interaction

# Verifier que tout est OK
php bin/console doctrine:schema:validate
```

### 3.4 Vider et Rechauffer le Cache

```bash
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod
chmod -R 755 var/cache var/log
```

---

## Etape 4 : Verifications Post-Deploiement

### Tests Fonctionnels

- Page d'accueil : `curl -I https://www.infpf.fr` (HTTP/2 200)
- Admin : `curl -I https://www.infpf.fr/admin` (HTTP/2 302 redirection login)
- Formulaire de contact : tester l'envoi d'email
- reCAPTCHA : verifier que le badge Google apparait

### Performance

```bash
# Tester le TTFB
curl -o /dev/null -s -w "TTFB: %{time_starttransfer}s\nTotal: %{time_total}s\n" https://www.infpf.fr
```

- Lighthouse : https://pagespeed.web.dev/ (Mobile >= 95, Desktop >= 97)

### Monitoring

- Sentry : https://sentry.io (verifier pas d'erreurs)
- UptimeRobot : https://uptimerobot.com (verifier status "Up")
- Google Analytics : https://analytics.google.com (verifier trafic)

---

## Rollback (En Cas de Probleme)

### Rollback Git

```bash
# 1. Trouver le dernier commit stable
git log --oneline -n 10

# 2. Rollback vers ce commit
git reset --hard COMMIT_HASH

# 3. Redeployer
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
```

### Restauration Base de Donnees

```bash
ls -lh /home/u665392393/backups/infpf/
mysql -u USERNAME -p DATABASE_NAME < /home/u665392393/backups/infpf/pre-deploy-YYYYMMDD.sql
```

---

## Checklist Finale

- [ ] Site accessible : https://www.infpf.fr (HTTP/2 200)
- [ ] HTTPS force (redirection HTTP vers HTTPS)
- [ ] Admin accessible : https://www.infpf.fr/admin
- [ ] Formulaire de contact fonctionne
- [ ] reCAPTCHA v3 actif
- [ ] Rate limiting actif
- [ ] Pages d'erreur personnalisees (404, 500)
- [ ] WebP servi automatiquement
- [ ] OPcache active
- [ ] HSTS active
- [ ] Lighthouse >= 95 (Mobile), >= 97 (Desktop)
- [ ] Sentry ne remonte pas d'erreurs
- [ ] UptimeRobot en "Up"
- [ ] Google Analytics recoit du trafic
- [ ] Backups automatiques configures (Cron)
- [ ] Fichiers de test supprimes
- [ ] Permissions correctes (755 var/, 775 uploads/)

---

**Date de deploiement** : _____________  
**Deploye par** : _____________  
**Version** : 2.0 - Production Ready  
**Commit Hash** : _____________
