# 🚀 Guide de Déploiement - INFPF.fr

## 📦 Déploiement sur le Serveur de Production

### 🔧 Prérequis

- Accès SSH au serveur Hostinger
- Git configuré
- Composer installé
- PHP 8.2+

---

## 🚀 Étapes de Déploiement

### 1️⃣ Connexion au Serveur

```bash
# Se connecter en SSH
ssh u665392393@infpf.fr

# Aller dans le répertoire du site
cd /home/u665392393/domains/infpf.fr/dev
```

### 2️⃣ Récupérer les Dernières Modifications

```bash
# Vérifier la branche actuelle
git branch

# Se positionner sur la bonne branche
git checkout feature/performance-security-seo-optimization

# Récupérer les dernières modifications
git pull origin feature/performance-security-seo-optimization
```

### 3️⃣ Installer/Mettre à Jour les Dépendances

```bash
# Installer les dépendances Composer
composer install --no-dev --optimize-autoloader

# Ou mettre à jour si nécessaire
composer update --no-dev --optimize-autoloader
```

### 4️⃣ Configuration de l'Environnement

```bash
# Créer/éditer le fichier .env.local pour la production
nano .env.local
```

**Contenu minimal de `.env.local` :**

```env
###> symfony/framework-bundle ###
APP_ENV=prod
APP_SECRET=YOUR_SECRET_HERE
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0&charset=utf8mb4"
###< doctrine/doctrine-bundle ###

###> sentry/sentry-symfony ###
SENTRY_DSN="https://b094d6d36a70d04dff26b577b8dc475f@o4510312920252416.ingest.de.sentry.io/4510312924512336"
###< sentry/sentry-symfony ###

# Désactiver le mode debug
APP_DEBUG=0
```

### 5️⃣ Vider le Cache et Préparer l'Application

```bash
# Vider le cache de production
php bin/console cache:clear --env=prod --no-debug

# Préchauffer le cache
php bin/console cache:warmup --env=prod --no-debug

# Exécuter les migrations (si nécessaire)
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
```

### 6️⃣ Configurer les Permissions

```bash
# Donner les bonnes permissions aux répertoires
chmod -R 755 var/cache var/log

# Rendre le script de backup exécutable
chmod +x bin/backup-database.sh
```

### 7️⃣ Tester le Script de Backup

```bash
# Créer le répertoire de backup s'il n'existe pas
mkdir -p /home/u665392393/backups

# Tester le script
./bin/backup-database.sh

# Vérifier que le backup a été créé
ls -lh /home/u665392393/backups/

# Vérifier les logs
cat /home/u665392393/backups/backup.log
```

### 8️⃣ Configurer le Cron Job dans hPanel

⚠️ **Important** : Sur Hostinger, `crontab` n'est pas disponible en ligne de commande.

**Configuration via hPanel :**

1. Se connecter à https://hpanel.hostinger.com/
2. Sélectionner l'hébergement `infpf.fr`
3. Aller dans **"Advanced"** → **"Cron Jobs"**
4. Cliquer sur **"Create New Cron Job"**
5. Configurer :

```
Type: Custom
Commande: /usr/bin/bash /home/u665392393/domains/infpf.fr/dev/bin/backup-database.sh
Fréquence: Daily at 02:00
Email: elyes@xeilos.fr (pour recevoir les notifications)
```

6. Sauvegarder

---

## ✅ Validation Post-Déploiement

### 1️⃣ Vérifier l'Environnement

```bash
# Vérifier que l'application est en mode production
php bin/console about
```

**Attendu :**
- Environment: `prod`
- Debug: `false`

### 2️⃣ Tester la Page d'Erreur 404 Personnalisée

```bash
# Accéder à une page inexistante
curl https://dev.infpf.fr/page-qui-nexiste-pas
```

**Attendu :** La page d'erreur personnalisée avec le design du site

### 3️⃣ Tester Sentry

```bash
# Accéder à la page de test Sentry
curl https://dev.infpf.fr/test-sentry.php
```

**Puis :**
1. Ouvrir https://sentry.io/organizations/infpf/projects/php-symfony/
2. Vérifier qu'une erreur de test apparaît
3. **⚠️ Supprimer le fichier de test** : `rm public/test-sentry.php`

### 4️⃣ Tester le Rate Limiting

```bash
# Envoyer 6 requêtes rapidement au formulaire de contact
for i in {1..6}; do
  curl -X POST https://dev.infpf.fr/contactez-nous \
    -d "nom=Test&email=test@test.com&message=Test" \
    -i | grep -E "HTTP|X-RateLimit"
  echo "---"
done
```

**Attendu :**
- Les 5 premières requêtes : `HTTP/2 200` avec headers `X-RateLimit-*`
- La 6ème requête : `HTTP/2 429` (Too Many Requests)

### 5️⃣ Vérifier les Backups

```bash
# Attendre le lendemain de la configuration du cron
# Puis vérifier
ls -lh /home/u665392393/backups/
cat /home/u665392393/backups/backup.log
```

**Attendu :** Un fichier `.sql.gz` par jour avec timestamp

### 6️⃣ Vérifier les Logs

```bash
# Logs d'application
tail -f var/log/prod.log

# Logs des erreurs
tail -f var/log/error.log
```

---

## 🔄 Déploiement des Mises à Jour

Pour chaque nouvelle mise à jour :

```bash
# 1. Récupérer les modifications
cd /home/u665392393/domains/infpf.fr/dev
git pull origin feature/performance-security-seo-optimization

# 2. Mettre à jour les dépendances
composer install --no-dev --optimize-autoloader

# 3. Migrations (si nécessaire)
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# 4. Vider le cache
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

# 5. Vérifier
php bin/console about
```

---

## 🐛 Résolution de Problèmes

### Problème : Cache en Mode Dev Persistant

**Solution :**
```bash
rm -rf var/cache/*
php bin/console cache:clear --env=prod
```

### Problème : Permissions Refusées

**Solution :**
```bash
chmod -R 755 var/cache var/log
chown -R u665392393:u665392393 var/cache var/log
```

### Problème : Page d'Erreur Symfony Visible

**Vérifier :**
```bash
cat .env.local | grep APP_ENV
# Doit afficher : APP_ENV=prod
```

### Problème : Sentry Ne Reçoit Pas d'Événements

**Vérifier :**
1. `SENTRY_DSN` dans `.env.local`
2. Sentry est bien configuré dans `config/packages/sentry.yaml`
3. Les erreurs sont bien loggées : `tail var/log/prod.log`

### Problème : Rate Limiting Ne Fonctionne Pas

**Vérifier :**
1. Le composant Lock est installé : `composer show symfony/lock`
2. Le cache est vidé : `php bin/console cache:clear --env=prod`
3. Les services sont bien enregistrés : `php bin/console debug:container limiter`

---

## 📋 Checklist Finale

Avant de mettre en production :

- [ ] ✅ `APP_ENV=prod` dans `.env.local`
- [ ] ✅ `APP_DEBUG=0` dans `.env.local`
- [ ] ✅ Toutes les variables d'environnement configurées
- [ ] ✅ Cache vidé et préchauffé
- [ ] ✅ Migrations exécutées
- [ ] ✅ Page 404 personnalisée visible
- [ ] ✅ Sentry reçoit les événements
- [ ] ✅ Rate limiting fonctionne (429 après 5 requêtes)
- [ ] ✅ Script de backup testé
- [ ] ✅ Cron job configuré dans hPanel
- [ ] ✅ Fichier `test-sentry.php` supprimé
- [ ] ✅ Logs vérifiés
- [ ] ✅ HTTPS/SSL actif
- [ ] ✅ Headers de sécurité HSTS actifs

---

## 🎯 Commandes Rapides de Référence

```bash
# Statut de l'application
php bin/console about

# Vider le cache
php bin/console cache:clear --env=prod

# Lister les routes
php bin/console debug:router

# Tester la base de données
php bin/console doctrine:schema:validate

# Vérifier les services
php bin/console debug:container

# Tester le backup
./bin/backup-database.sh

# Voir les logs en temps réel
tail -f var/log/prod.log
```

---

## 📞 Support

En cas de problème :
- **Développeur** : elyes@xeilos.fr
- **Hébergeur** : Support Hostinger via hPanel
- **Documentation** : https://symfony.com/doc/current/deployment.html

---

**Dernière mise à jour** : 5 novembre 2025


