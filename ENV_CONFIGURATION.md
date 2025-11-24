#  Configuration des Variables d'Environnement

##  Variables Requises pour le Projet

Pour que le projet fonctionne correctement, vous devez configurer ces variables dans le fichier `.env.local` sur votre serveur.

---

##  Configuration Complète `.env.local`

Créez le fichier `/home/u665392393/domains/infpf.fr/dev/.env.local` avec le contenu suivant :

```env
###> symfony/framework-bundle ###
APP_ENV=prod
APP_SECRET=YOUR_SECRET_HERE_CHANGE_THIS
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0&charset=utf8mb4"
###< doctrine/doctrine-bundle ###

###> sentry/sentry-symfony ###
SENTRY_DSN="https://b094d6d36a70d04dff26b577b8dc475f@o4510312920252416.ingest.de.sentry.io/4510312924512336"
###< sentry/sentry-symfony ###

###> symfony/lock ###
# Utilise le système de fichiers pour le rate limiting
# Options : flock (fichiers), semaphore (sémaphores), redis (Redis)
LOCK_DSN=flock
###< symfony/lock ###

# Désactiver le mode debug en production
APP_DEBUG=0
```

---

##  Explication des Variables

### 1. `APP_ENV`
- **Valeurs** : `dev`, `prod`, `test`
- **Production** : `prod`
- **Développement** : `dev`
- **Effet** : 
  - En `prod` : pages d'erreur personnalisées, cache activé, debug désactivé
  - En `dev` : barre de debug Symfony, erreurs détaillées

### 2. `APP_SECRET`
- **Valeur** : Une chaîne aléatoire longue (32+ caractères)
- **Génération** : `php bin/console secrets:generate-keys`
- **Important** : Doit rester secret et différent pour chaque environnement

### 3. `DATABASE_URL`
- **Format** : `mysql://user:password@host:port/database?serverVersion=X.X`
- **Exemple** : `mysql://infpf:mdp123@127.0.0.1:3306/infpf_db?serverVersion=8.0`
- **Obtenir** : Panneau Hostinger → Bases de données

### 4. `SENTRY_DSN`
- **Valeur actuelle** : `https://b094d6d36a70d04dff26b577b8dc475f@o4510312920252416.ingest.de.sentry.io/4510312924512336`
- **Obtenir** : https://sentry.io/ → Settings → Client Keys (DSN)
- **Effet** : Envoie toutes les erreurs à Sentry pour surveillance

### 5. `LOCK_DSN`
- **Valeurs possibles** :
  - `flock` : Utilise les fichiers (simple, recommandé)
  - `semaphore` : Utilise les sémaphores système
  - `redis://localhost` : Utilise Redis (performances)
- **Recommandé** : `flock` pour la simplicité
- **Effet** : Permet le rate limiting (limite les requêtes)

### 6. `APP_DEBUG`
- **Valeurs** : `0` (désactivé), `1` (activé)
- **Production** : `0` (OBLIGATOIRE)
- **Développement** : `1` (optionnel)
- **Effet** : Affiche ou masque les erreurs détaillées

---

##  Vérification de la Configuration

Après avoir créé `.env.local`, vérifiez la configuration :

```bash
cd /home/u665392393/domains/infpf.fr/dev

# Vérifier l'environnement
php bin/console about

# Vider le cache
php bin/console cache:clear

# Tester une route
curl -I https://dev.infpf.fr/test/error/404
```

---

##  Résolution de Problèmes

### Erreur : "Environment variable not found: X"

**Cause** : Variable manquante dans `.env.local`

**Solution** :
1. Ajoutez la variable dans `.env.local`
2. Videz le cache : `php bin/console cache:clear`

### Erreur 500 sur toutes les pages

**Cause** : `LOCK_DSN` ou `SENTRY_DSN` manquant

**Solution** :
```bash
echo 'LOCK_DSN=flock' >> .env.local
echo 'SENTRY_DSN="YOUR_DSN_HERE"' >> .env.local
php bin/console cache:clear
```

### Pages d'erreur Symfony visibles au lieu des pages personnalisées

**Cause** : `APP_ENV=dev` au lieu de `prod`

**Solution** :
```bash
# Modifier .env.local
APP_ENV=prod
APP_DEBUG=0

# Vider le cache
php bin/console cache:clear --env=prod
```

---

##  Sécurité

 **IMPORTANT** :

1. **NE JAMAIS** commiter `.env.local` dans Git
2. **NE JAMAIS** partager votre `APP_SECRET`
3. **NE JAMAIS** exposer `DATABASE_URL` publiquement
4. **TOUJOURS** utiliser `APP_DEBUG=0` en production

Le fichier `.env.local` est déjà dans `.gitignore` et ne sera jamais commité.

---

## 📚 Références

- [Symfony Environment Variables](https://symfony.com/doc/current/configuration.html#configuration-based-on-environment-variables)
- [Sentry Configuration](https://docs.sentry.io/platforms/php/guides/symfony/)
- [Symfony Lock Component](https://symfony.com/doc/current/components/lock.html)

---

**Dernière mise à jour** : 5 novembre 2025, 17h20

