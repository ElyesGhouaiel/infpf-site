# 📊 Configuration Sentry & Monitoring

## 🎯 Objectif

Sentry et Monolog sont maintenant configurés pour le monitoring des erreurs en production.

---

## 🔧 Configuration Sentry

### 1. Créer un compte Sentry (GRATUIT)

1. Aller sur [sentry.io](https://sentry.io/)
2. Créer un compte gratuit
3. Créer un nouveau projet "Symfony"
4. Récupérer votre **DSN** (Data Source Name)

### 2. Configurer la variable d'environnement

Ajouter dans votre fichier `.env.local` :

```bash
###> sentry/sentry-symfony ###
SENTRY_DSN=https://YOUR_KEY@o123456.ingest.sentry.io/123456
###< sentry/sentry-symfony ###
```

**Remplacer** `YOUR_KEY` et les chiffres par votre DSN Sentry réel.

### 3. Tester l'installation

Créer une erreur volontaire pour tester :

```php
// Dans un contrôleur temporaire
throw new \Exception('Test Sentry - Erreur volontaire');
```

Vous devriez voir l'erreur apparaître dans votre tableau de bord Sentry !

---

## 📝 Logs structurés avec Monolog

### Production (`config/packages/prod/monolog.yaml`)

- ✅ **Logs vers fichier** : `var/log/prod.log` (format JSON)
- ✅ **Logs vers Sentry** : Erreurs critiques uniquement
- ✅ **Déduplication** : Évite les doublons d'erreurs
- ✅ **Filtrage** : Exclut les erreurs 404/405

### Développement (`config/packages/dev/monolog.yaml`)

- ✅ **Logs vers fichier** : `var/log/dev.log` (format JSON)
- ✅ **Logs console** : Affichage en temps réel
- ✅ **Deprecation warnings** : `var/log/deprecations.log`

---

## 🚀 Fonctionnalités activées

### 🔍 **Erreurs capturées automatiquement**
- Exceptions PHP
- Erreurs fatales
- Warnings et Notices (configurables)
- Erreurs 500
- Erreurs de base de données

### 📊 **Contexte automatique**
- URL de la requête
- Méthode HTTP (GET, POST, etc.)
- Headers HTTP
- User-Agent
- IP du client
- Utilisateur connecté (si auth)
- Stack trace complète

### 🎯 **Performance Monitoring**
- Traces des requêtes lentes
- Profiling CPU/Mémoire
- Monitoring des queries SQL
- Détection des N+1 queries

### 📈 **Dashboard Sentry**
- Graphiques des erreurs
- Alertes par email/Slack
- Historique des releases
- Tendances et patterns
- Erreurs résolues/non résolues

---

## 🛡️ **Sécurité et Confidentialité**

✅ **send_default_pii: false** → Pas de données personnelles envoyées par défaut  
✅ Filtrage des tokens et mots de passe automatique  
✅ Hébergement dans l'EU disponible  
✅ Conformité RGPD

---

## 💡 Bonnes pratiques

### 1. **Utiliser les niveaux de log appropriés**

```php
$logger->debug('Info de débogage');
$logger->info('Info générale');
$logger->notice('Événement notable');
$logger->warning('Warning non bloquant');
$logger->error('Erreur récupérable');
$logger->critical('Erreur critique');
$logger->alert('Action immédiate requise');
$logger->emergency('Système inutilisable');
```

### 2. **Ajouter du contexte aux logs**

```php
$logger->error('Paiement échoué', [
    'user_id' => $user->getId(),
    'amount' => $amount,
    'payment_method' => 'stripe',
    'error_code' => $e->getCode()
]);
```

### 3. **Marquer les erreurs comme résolues**

Dans Sentry, marquez les erreurs comme "Resolved" une fois corrigées.

### 4. **Créer des releases**

```bash
# Avant chaque déploiement
export SENTRY_RELEASE="v1.0.1"
# Sentry trackera les erreurs par version
```

---

## 📞 Support

- **Documentation Sentry** : [docs.sentry.io](https://docs.sentry.io/)
- **Documentation Monolog** : [github.com/Seldaek/monolog](https://github.com/Seldaek/monolog)
- **Contact admin** : elyes@xeilos.fr

---

## ✅ Checklist finale

- [ ] Compte Sentry créé
- [ ] DSN configuré dans `.env.local`
- [ ] Test d'erreur volontaire effectué
- [ ] Erreur visible dans Sentry
- [ ] Logs visibles dans `var/log/prod.log`
- [ ] Alertes par email configurées
- [ ] Intégration Slack (optionnel)

---

**Date de configuration** : 2025-11-05  
**Version Sentry** : 5.6.0  
**Version Monolog** : 3.6.0

