# Guide de Déploiement - Site INFPF

## Pré-requis

- PHP 8.1 ou supérieur
- MySQL 8.0 ou supérieur
- Composer 2.x
- Apache 2.4+ avec mod_rewrite activé
- Accès SSH au serveur
- Git installé

---

## Installation Initiale

### 1. Cloner le projet

```bash
cd /home/u665392393/domains/infpf.fr
git clone <repository-url> public_html
cd public_html
```

### 2. Configurer l'environnement

```bash
# Copier le fichier d'exemple
cp .env.example .env

# Éditer les variables d'environnement
nano .env
```

Variables critiques à configurer :
- `APP_ENV=prod`
- `APP_SECRET=` (générer avec `openssl rand -hex 32`)
- `DATABASE_URL=` (connexion MySQL)
- `MAILER_DSN=` (configuration email)
- `RECAPTCHA3_KEY=` et `RECAPTCHA3_SECRET=`
- `SENTRY_DSN=` (monitoring erreurs)

### 3. Installer les dépendances

```bash
composer install --no-dev --optimize-autoloader
```

### 4. Configurer la base de données

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# (Optionnel) Charger les données de test
php bin/console doctrine:fixtures:load --no-interaction
```

### 5. Configurer les permissions

```bash
# Permissions pour les répertoires de cache et logs
chmod -R 775 var/cache var/log
chown -R www-data:www-data var/cache var/log

# Permissions pour les uploads
chmod -R 775 public/uploads
chown -R www-data:www-data public/uploads
```

### 6. Vider le cache

```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

### 7. Vérifier la configuration

```bash
php bin/console about
php bin/console debug:config
```

---

## Déploiement d'une Mise à Jour

### Méthode 1 : Déploiement manuel

```bash
# 1. Se connecter au serveur
ssh u665392393@infpf.fr

# 2. Aller dans le répertoire du projet
cd /home/u665392393/domains/infpf.fr/public_html

# 3. Sauvegarder la base de données
./scripts/backup-database.sh

# 4. Récupérer les dernières modifications
git fetch origin
git pull origin main

# 5. Installer/mettre à jour les dépendances
composer install --no-dev --optimize-autoloader

# 6. Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# 7. Vider le cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 8. Vérifier que tout fonctionne
php bin/console about
curl -I https://infpf.fr
```

### Méthode 2 : Utiliser le script de déploiement

```bash
./deploy-to-prod.sh
```

---

## Workflow Git

### Branches

- `main` : Production (infpf.fr)
- `dev` : Pré-production (dev.infpf.fr)
- `feature/*` : Développement de fonctionnalités

### Processus de déploiement

1. **Développement sur feature branch**
   ```bash
   git checkout -b feature/ma-fonctionnalite
   # ... développement ...
   git add .
   git commit -m "Description des changements"
   git push origin feature/ma-fonctionnalite
   ```

2. **Merge vers dev pour tests**
   ```bash
   git checkout dev
   git merge feature/ma-fonctionnalite
   git push origin dev
   # Tester sur dev.infpf.fr
   ```

3. **Merge vers main pour production**
   ```bash
   git checkout main
   git merge dev
   git push origin main
   # Déployer sur infpf.fr
   ```

---

## Sauvegardes

### Sauvegarde manuelle

```bash
# Base de données
./scripts/backup-database.sh

# Fichiers
tar -czf backup-files-$(date +%Y%m%d).tar.gz \
  public/uploads \
  .env \
  var/log
```

### Sauvegarde automatique (cron)

Ajouter au crontab :

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne pour une sauvegarde quotidienne à 3h du matin
0 3 * * * /home/u665392393/domains/infpf.fr/public_html/scripts/backup-database.sh
```

### Restauration d'une sauvegarde

```bash
# Trouver la sauvegarde
ls -lh backups/

# Décompresser
gunzip backups/backup-20251120-030000.sql.gz

# Restaurer
mysql -u USERNAME -p DATABASE_NAME < backups/backup-20251120-030000.sql
```

---

## Monitoring

### Health Check manuel

```bash
./scripts/health-check.sh
```

### Health Check automatique (cron)

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne pour une vérification toutes les 5 minutes
*/5 * * * * /home/u665392393/domains/infpf.fr/public_html/scripts/health-check.sh
```

### Consulter les logs

```bash
# Logs Symfony
tail -f var/log/prod.log

# Logs Apache
tail -f /var/log/apache2/error.log
tail -f /var/log/apache2/access.log

# Logs de sauvegarde
tail -f var/log/backup.log

# Logs de health check
tail -f var/log/health-check.log
```

---

## Dépannage

### Le site affiche une erreur 500

1. Vérifier les logs :
   ```bash
   tail -100 var/log/prod.log
   tail -100 /var/log/apache2/error.log
   ```

2. Vérifier les permissions :
   ```bash
   chmod -R 775 var/cache var/log
   chown -R www-data:www-data var/cache var/log
   ```

3. Vider le cache :
   ```bash
   rm -rf var/cache/*
   php bin/console cache:clear --env=prod
   ```

### Le site est lent

1. Vérifier OPcache :
   ```bash
   php -i | grep opcache
   ```

2. Vérifier l'espace disque :
   ```bash
   df -h
   ```

3. Analyser les logs de performance :
   ```bash
   grep "Notified Symfony about the request" var/log/prod.log | tail -50
   ```

### Erreur de base de données

1. Vérifier la connexion :
   ```bash
   php bin/console doctrine:database:create --if-not-exists
   ```

2. Vérifier les migrations :
   ```bash
   php bin/console doctrine:migrations:status
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

3. Vérifier les credentials dans `.env`

### CSS/JS ne se charge pas

1. Vérifier les permissions :
   ```bash
   chmod -R 755 public/css public/js public/images
   ```

2. Vider le cache navigateur (Ctrl+Shift+R)

3. Vérifier le `.htaccess` :
   ```bash
   cat public/.htaccess | grep RewriteRule
   ```

---

## Rollback (Retour en arrière)

### Rollback Git

```bash
# Voir l'historique
git log --oneline -10

# Revenir au commit précédent
git reset --hard <commit-hash>

# Forcer le push (ATTENTION : destructif)
git push origin main --force
```

### Rollback Base de données

```bash
# Restaurer la dernière sauvegarde
cd backups
gunzip $(ls -t *.sql.gz | head -1)
mysql -u USERNAME -p DATABASE_NAME < $(ls -t *.sql | head -1)
```

---

## Checklist de Déploiement

Avant chaque déploiement en production :

- [ ] Tests effectués sur `dev.infpf.fr`
- [ ] Sauvegarde de la base de données effectuée
- [ ] `.env` vérifié (APP_ENV=prod, APP_DEBUG=0)
- [ ] Migrations testées
- [ ] Cache vidé
- [ ] Vérification post-déploiement effectuée
- [ ] Logs vérifiés (pas d'erreurs)
- [ ] Test de navigation sur le site
- [ ] Test des formulaires critiques
- [ ] Vérification PageSpeed (score > 90)

---

## Contacts

- **Développeur** : [Votre nom]
- **Hébergeur** : Hostinger
- **Support technique** : support@hostinger.com

---

*Dernière mise à jour : 20 novembre 2025*
