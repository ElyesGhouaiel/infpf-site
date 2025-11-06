# 🚀 Guide de Déploiement INFPF - Production

Ce guide détaille **étape par étape** le déploiement du site INFPF sur Hostinger en production.

---

## 📋 Checklist Pré-Déploiement

Avant de déployer sur `infpf.fr` (production), vérifie que :

### ✅ Tests & Validation

- [ ] Tous les tests PHPUnit passent (`vendor/bin/phpunit`)
- [ ] Le site fonctionne sur `dev.infpf.fr` sans erreur
- [ ] Lighthouse score ≥ 95 (Mobile) et ≥ 97 (Desktop)
- [ ] Tous les formulaires fonctionnent (contact, inscription, etc.)
- [ ] reCAPTCHA v3 fonctionne
- [ ] Rate limiting testé (anti-spam)
- [ ] Pages d'erreur personnalisées testées (404, 403, 500)

### ✅ Configuration

- [ ] `.env.local` créé sur production avec les bonnes valeurs
- [ ] SENTRY_DSN configuré (monitoring)
- [ ] GOOGLE_ANALYTICS_MEASUREMENT_ID configuré
- [ ] MAILER_DSN configuré (Gmail ou Mailgun)
- [ ] RECAPTCHA_SITE_KEY / RECAPTCHA_SECRET_KEY configurés
- [ ] DATABASE_URL configuré (MySQL production)
- [ ] APP_ENV=prod et APP_DEBUG=false

### ✅ Sécurité

- [ ] HSTS activé dans `.htaccess` (après test)
- [ ] Fichiers sensibles dans `.gitignore` (`.env.local`, `var/`)
- [ ] Permissions correctes (`755` pour `var/`, `775` pour `uploads/`)
- [ ] `opcache-check.php` supprimé
- [ ] `test-*.php` supprimés du `public/`

### ✅ Backups

- [ ] Backup de la base de données actuelle
- [ ] Backup des fichiers actuels (`public/uploads/`)
- [ ] Point de restauration créé

---

## 🔧 Étape 1 : Préparation Locale

### 1.1 Teste en Local / Dev

```bash
cd /home/u665392393/domains/infpf.fr/dev

# 1. Lance tous les tests
vendor/bin/phpunit

# 2. Vérifie les dépendances
composer validate
composer outdated

# 3. Vérifie la syntaxe YAML
php bin/console lint:yaml config/

# 4. Vérifie les routes
php bin/console debug:router

# 5. Vide et réchauffe le cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

### 1.2 Merge la Branche de Dev

```bash
# Si tu es sur une branche feature
git checkout main
git merge feature/performance-security-seo-optimization

# Résous les conflits si nécessaire
git status

# Commit et push
git push origin main
```

---

## 📦 Étape 2 : Backup Avant Déploiement

### 2.1 Backup Base de Données

```bash
# Via le script automatique
cd /home/u665392393/domains/infpf.fr/public_html
./bin/backup-database.sh

# Ou manuellement
mysqldump -u USERNAME -p DATABASE_NAME > /home/u665392393/backups/infpf/pre-deploy-$(date +%Y%m%d-%H%M%S).sql
```

### 2.2 Backup Fichiers

```bash
# Sauvegarde les uploads
tar -czf /home/u665392393/backups/infpf/uploads-backup-$(date +%Y%m%d).tar.gz \
    /home/u665392393/domains/infpf.fr/public_html/public/uploads/

# Sauvegarde .env.local
cp /home/u665392393/domains/infpf.fr/public_html/.env.local \
   /home/u665392393/backups/infpf/.env.local.backup
```

---

## 🚀 Étape 3 : Déploiement sur Production

### 3.1 Connexion SSH

```bash
ssh u665392393@your-domain.com
```

### 3.2 Pull les Changements

```bash
cd /home/u665392393/domains/infpf.fr/public_html

# 1. Vérifie la branche actuelle
git branch

# 2. Pull les derniers changements
git pull origin main

# Si erreur "uncommitted changes", stash-les :
git stash
git pull origin main
git stash pop
```

### 3.3 Installe les Dépendances (Prod Only)

```bash
# Installe uniquement les dépendances de production (sans dev)
composer install --no-dev --optimize-autoloader --classmap-authoritative

# Vérification
composer show | grep -i "dev"
# Ne devrait rien afficher (pas de packages dev)
```

### 3.4 Migrations Base de Données (Si Nécessaire)

```bash
# Vérifie les migrations en attente
php bin/console doctrine:migrations:status

# Exécute les migrations (mode non-interactif)
php bin/console doctrine:migrations:migrate --no-interaction

# Vérifie que tout est OK
php bin/console doctrine:schema:validate
```

### 3.5 Vide et Réchauffe le Cache

```bash
# Vide le cache de prod
php bin/console cache:clear --env=prod --no-warmup

# Réchauffe le cache
php bin/console cache:warmup --env=prod

# Vérifie les permissions
chmod -R 755 var/cache var/log
```

---

## 🖼️ Étape 4 : Optimisation des Images (WebP)

### 4.1 Vérifie que cwebp est Installé

```bash
cwebp -version
# Devrait afficher : libwebp 1.x.x
```

Si pas installé, contacte Hostinger support.

### 4.2 Convertis les Images

```bash
# Convertit uniquement les nouvelles images
./bin/optimize-images-webp.sh --new

# Ou reconvertis tout (si première fois)
./bin/optimize-images-webp.sh --all
```

### 4.3 Vérifie que WebP Fonctionne

```bash
# Teste une image
curl -H "Accept: image/webp" -I https://www.infpf.fr/img/logo.png

# Devrait retourner :
# Content-Type: image/webp   ← ✅
```

---

## 🔧 Étape 5 : Configuration OPcache

### 5.1 Vérifie OPcache (Temporairement)

```bash
# Accède au script de test
curl https://www.infpf.fr/opcache-check.php | jq

# Vérifie que "enabled": true
```

### 5.2 Supprime le Script de Test (Sécurité)

```bash
rm /home/u665392393/domains/infpf.fr/public_html/public/opcache-check.php
```

### 5.3 Redémarre PHP-FPM

Via **Hostinger hPanel** :
1. Connexion à **hPanel**
2. **Gestionnaire PHP** > **Redémarrer PHP-FPM**

---

## 🔐 Étape 6 : Activation Headers de Sécurité (HSTS)

⚠️ **Important** : Active HSTS uniquement si HTTPS fonctionne partout.

### 6.1 Teste HTTPS

```bash
curl -I https://www.infpf.fr
# Devrait retourner : HTTP/2 200
```

### 6.2 Active HSTS

```bash
# Édite .htaccess
nano /home/u665392393/domains/infpf.fr/public_html/public/.htaccess

# Décommente la ligne :
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

### 6.3 Teste les Headers

```bash
curl -I https://www.infpf.fr | grep -i "strict-transport"
# Devrait afficher : strict-transport-security: max-age=31536000; includeSubDomains; preload
```

---

## 📊 Étape 7 : Vérifications Post-Déploiement

### 7.1 Tests Fonctionnels

✅ **Page d'accueil**
```bash
curl -I https://www.infpf.fr
# HTTP/2 200
```

✅ **Admin**
```bash
curl -I https://www.infpf.fr/admin
# HTTP/2 302 (redirection vers login)
```

✅ **Formulaire de contact**
- Va sur https://www.infpf.fr/contact
- Envoie un email de test
- Vérifie la réception

✅ **reCAPTCHA**
- Vérifie que le badge Google apparaît
- Teste la soumission

✅ **Pages d'erreur**
- https://www.infpf.fr/404 → Page 404 personnalisée
- https://www.infpf.fr/test-error/500 → Page 500 personnalisée

### 7.2 Performance

```bash
# Teste le TTFB
curl -o /dev/null -s -w "TTFB: %{time_starttransfer}s\nTotal: %{time_total}s\n" https://www.infpf.fr

# Devrait afficher :
# TTFB: 0.12s
# Total: 0.85s
```

✅ **Lighthouse**
- Va sur https://pagespeed.web.dev/
- Teste `https://www.infpf.fr`
- Score attendu : Mobile ≥ 95, Desktop ≥ 97

### 7.3 Monitoring

✅ **Sentry**
- Va sur https://sentry.io
- Vérifie qu'il n'y a pas d'erreurs

✅ **UptimeRobot**
- Va sur https://uptimerobot.com
- Vérifie que le monitor est "Up" (vert)

✅ **Google Analytics**
- Va sur https://analytics.google.com
- Vérifie les visiteurs actifs (après 5-10 minutes)

---

## 🔄 Étape 8 : Automatisation (Cron Jobs)

### 8.1 Backup Quotidien

Via **Hostinger hPanel** :
1. **Cron Jobs** > **Ajouter une tâche**
2. **Commande** : `/home/u665392393/domains/infpf.fr/public_html/bin/backup-database.sh`
3. **Fréquence** : Quotidien à **02:00**
4. Sauvegarde

### 8.2 Optimisation WebP Hebdomadaire

Via **Hostinger hPanel** :
1. **Cron Jobs** > **Ajouter une tâche**
2. **Commande** : `/home/u665392393/domains/infpf.fr/public_html/bin/optimize-images-webp.sh --new`
3. **Fréquence** : Hebdomadaire (Dimanche à **03:00**)
4. Sauvegarde

---

## 🆘 Étape 9 : Rollback (En Cas de Problème)

### 9.1 Rollback Git

```bash
# 1. Trouve le dernier commit stable
git log --oneline -n 10

# 2. Rollback vers ce commit
git reset --hard COMMIT_HASH

# 3. Redéploie
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
```

### 9.2 Restauration Base de Données

```bash
# Trouve le dernier backup
ls -lh /home/u665392393/backups/infpf/

# Restaure
mysql -u USERNAME -p DATABASE_NAME < /home/u665392393/backups/infpf/pre-deploy-20251106.sql
```

### 9.3 Restauration Fichiers

```bash
# Restaure les uploads
tar -xzf /home/u665392393/backups/infpf/uploads-backup-20251106.tar.gz -C /

# Restaure .env.local
cp /home/u665392393/backups/infpf/.env.local.backup \
   /home/u665392393/domains/infpf.fr/public_html/.env.local
```

---

## 📞 Support

Si tu rencontres un problème :

1. **Vérifie les logs** :
   ```bash
   tail -f /home/u665392393/domains/infpf.fr/public_html/var/log/prod.log
   tail -f /home/u665392393/logs/error_log
   ```

2. **Sentry** : https://sentry.io (erreurs en temps réel)

3. **Hostinger Support** : https://support.hostinger.com

4. **Développeur** : elyes@xeilos.fr

---

## 🎯 Checklist Finale

Une fois le déploiement terminé, vérifie que :

- [ ] ✅ Site accessible : https://www.infpf.fr (HTTP/2 200)
- [ ] ✅ HTTPS forcé (redirection HTTP → HTTPS)
- [ ] ✅ Admin accessible : https://www.infpf.fr/admin
- [ ] ✅ Formulaire de contact fonctionne
- [ ] ✅ reCAPTCHA v3 actif
- [ ] ✅ Rate limiting actif (teste 6 soumissions rapidement)
- [ ] ✅ Pages d'erreur personnalisées (404, 500)
- [ ] ✅ WebP servi automatiquement (curl test)
- [ ] ✅ OPcache activé (opcache_get_status())
- [ ] ✅ HSTS activé (curl -I test)
- [ ] ✅ Lighthouse ≥ 95 (Mobile), ≥ 97 (Desktop)
- [ ] ✅ Sentry ne remonte pas d'erreurs
- [ ] ✅ UptimeRobot en "Up"
- [ ] ✅ Google Analytics reçoit du trafic
- [ ] ✅ Backups automatiques configurés (Cron)
- [ ] ✅ Fichiers de test supprimés (`opcache-check.php`, etc.)
- [ ] ✅ Permissions correctes (`755` var/, `775` uploads/)

---

**Date de déploiement** : _____________  
**Déployé par** : _____________  
**Version** : 2.0 - Production Ready  
**Commit Hash** : _____________

---

**Félicitations ! 🎉 Le site INFPF est maintenant en production !**




