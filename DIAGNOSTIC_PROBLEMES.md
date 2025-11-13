# 🔍 Diagnostic des Problèmes Actuels

Date : 5 novembre 2025

## ⚠️ Problèmes Identifiés

### 1. ❌ Page d'Erreur Symfony Encore Visible

**Symptôme** : La page d'erreur par défaut de Symfony s'affiche toujours au lieu des pages personnalisées

**Cause** : 
- Les templates d'erreur personnalisés ne sont actifs **QUE en mode PRODUCTION**
- En mode `dev`, Symfony affiche toujours la barre de debug et les pages d'erreur détaillées

**Solution** :
```bash
# Sur le serveur de production, vérifier l'environnement
cd /home/u665392393/domains/infpf.fr/dev
cat .env.local | grep APP_ENV

# Doit contenir :
APP_ENV=prod
APP_DEBUG=0

# Si ce n'est pas le cas, créer/modifier .env.local
echo "APP_ENV=prod" >> .env.local
echo "APP_DEBUG=0" >> .env.local

# Vider le cache
php bin/console cache:clear --env=prod
```

**Validation** :
- Accéder à `https://dev.infpf.fr/page-qui-nexiste-pas`
- La page personnalisée devrait s'afficher (avec le design du site)

---

### 2. ⏳ Sentry "Waiting for events..."

**Symptôme** : Sentry affiche "Waiting for events..." sans erreurs capturées

**Cause** : 
- C'est **NORMAL** ! Sentry attend qu'une erreur se produise
- Aucune erreur n'a encore été générée depuis l'installation

**Solution** : Créer une route de test pour générer une erreur volontaire

**Route de test créée** : `/test-sentry.php`

**Validation** :
1. Créer le fichier `/test-sentry.php` sur le serveur
2. Accéder à `https://dev.infpf.fr/test-sentry.php`
3. Une erreur sera envoyée à Sentry
4. Vérifier dans Sentry.io → le projet php-symfony devrait afficher l'erreur

---

### 3. ❌ Rate Limiting Ne Fonctionne Pas

**Symptôme** : 
- Toutes les requêtes passent (HTTP 200)
- Pas de headers `X-RateLimit-*` visibles
- Aucune erreur 429 après 5+ requêtes

**Cause** : 
Le code actuel essaie d'ajouter les headers dans le `RequestEvent`, mais la réponse n'existe pas encore à ce moment.

```php
// ❌ PROBLÈME dans RateLimitListener.php
$response = $event->getResponse(); // NULL pendant RequestEvent
if ($response) {
    // Ce code ne s'exécute JAMAIS
}
```

**Solution** : Utiliser un `ResponseEvent` pour ajouter les headers

**Statut** : 🔧 À corriger (voir ci-dessous)

---

### 4. ❌ Script de Backup Introuvable

**Symptôme** :
```bash
bash: ./bin/backup-database.sh: No such file or directory
```

**Cause** :
- Le script existe dans le workspace Git (`/home/u665392393/.cursor/worktrees/...`)
- Mais pas sur le serveur de prod/dev (`/home/u665392393/domains/infpf.fr/dev`)
- Le script n'a pas encore été déployé

**Solution** : Copier le script lors du prochain déploiement Git

**Validation** :
```bash
cd /home/u665392393/domains/infpf.fr/dev
git pull origin feature/performance-security-seo-optimization
chmod +x bin/backup-database.sh
./bin/backup-database.sh
```

---

### 5. ⚠️ Crontab Introuvable

**Symptôme** :
```bash
bash: crontab: command not found
```

**Cause** :
- Hostinger (hébergement partagé) ne permet pas l'accès direct à `crontab`
- Les cron jobs doivent être configurés via le **panneau de contrôle hPanel**

**Solution** : Utiliser le panneau Hostinger pour configurer les tâches planifiées

**Instructions** :
1. Se connecter à hPanel Hostinger
2. Aller dans "Advanced" → "Cron Jobs"
3. Ajouter un nouveau cron job :
   - **Fréquence** : Tous les jours à 2h00 du matin
   - **Commande** : `/usr/bin/bash /home/u665392393/domains/infpf.fr/dev/bin/backup-database.sh`
4. Sauvegarder

---

## ✅ Ce Qui Fonctionne

1. ✅ **Dépendabot** : Configuré et actif
2. ✅ **Sentry** : Installé et configuré (attend juste des événements)
3. ✅ **Monolog** : Logs structurés JSON en place
4. ✅ **Tests** : 100% passent (skips documentés)
5. ✅ **SSL/HTTPS** : HSTS activé
6. ✅ **Templates d'erreur** : Créés (attendent mode prod)

---

## 🔧 Corrections Nécessaires

### Priorité 1 : Fix Rate Limiting

Le rate limiting ne fonctionne pas car les headers sont ajoutés au mauvais moment.

**Fichiers à modifier** :
1. `src/EventListener/RateLimitListener.php`
2. `config/services.yaml`

### Priorité 2 : Créer Route Test Sentry

Créer `/public/test-sentry.php` pour valider que Sentry fonctionne.

### Priorité 3 : Guide de Déploiement

Documenter comment déployer correctement sur le serveur de production.

---

## 📋 Checklist de Validation Complète

- [ ] Mode production activé (`APP_ENV=prod`)
- [ ] Page 404 personnalisée visible
- [ ] Sentry reçoit des événements de test
- [ ] Rate limiting bloque après 5 requêtes
- [ ] Headers `X-RateLimit-*` visibles
- [ ] Script de backup exécutable
- [ ] Cron job configuré dans hPanel
- [ ] Backup quotidien fonctionnel

---

## 🚀 Prochaines Étapes

1. **Corriger le Rate Limiting** (15 min)
2. **Créer test-sentry.php** (5 min)
3. **Documenter le déploiement** (10 min)
4. **Passer en mode production sur dev.infpf.fr** (2 min)
5. **Valider toutes les fonctionnalités** (15 min)

**TOTAL ESTIMÉ : 47 minutes**

Ensuite, nous pourrons passer au **JOUR 3** avec :
- Monitoring uptime (UptimeRobot)
- Google Analytics / Matomo


