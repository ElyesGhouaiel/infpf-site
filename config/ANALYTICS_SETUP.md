# Configuration du système d'Analytics & Exclusions RGPD

## 🔒 Exclusion de votre trafic admin

Votre trafic peut être exclu de plusieurs manières (par ordre de priorité) :

### 1. ✅ Exclusion automatique par rôle (DÉJÀ ACTIF)
- Si vous êtes connecté avec le rôle `ROLE_ADMIN`, **votre trafic est automatiquement exclu**
- Aucune configuration nécessaire

### 2. ✅ Exclusion des routes privées (DÉJÀ ACTIF)
- Toutes les pages `/admin/*`, `/backoffice/*`, `/dashboard/*`, `/api/*` sont exclues
- Aucune configuration nécessaire

### 3. Cookie développeur (opt-out permanent)

Pour bloquer complètement le tracking sur votre navigateur :

```javascript
// Dans la console du navigateur :
window.INFPFAnalytics.setDevCookie();
// Le cookie xeilos_dev=1 sera créé et bloquera tout tracking

// Pour réactiver le tracking :
window.INFPFAnalytics.removeDevCookie();
```

### 4. Exclusion par IP publique

#### Trouver votre IP publique :
1. Allez sur https://www.whatismyip.com/
2. Copiez votre adresse IP (ex: `203.0.113.45`)

⚠️ **Attention** : `192.168.1.119` est une IP **privée/locale** et n'est **PAS** visible côté serveur.

#### Configurer l'exclusion :
Éditez le fichier `config/analytics_config.php` :

```php
'excluded_public_ips' => [
    '203.0.113.45',  // Remplacez par VOTRE IP publique
    // Ajoutez d'autres IPs si nécessaire
],
```

---

## 🍪 Comportement RGPD

### Mode "Tout refuser" (anonyme strict)
- ✅ Aucun cookie non essentiel
- ✅ Aucun ID de session
- ✅ Aucune IP stockée
- ✅ Aucun fingerprinting
- ✅ Pas de tracking individuel
- ✅ Agrégation locale uniquement

### Mode "Tout accepter" (conformité RGPD)
- ✅ ID de session aléatoire (pas de PII)
- ✅ IP anonymisée (dernier octet masqué)
- ✅ Pays (géoloc serveur, IP non stockée)
- ✅ Device/OS/Navigateur
- ✅ Langue navigateur
- ✅ Source de trafic (referrer)
- ✅ Paramètres UTM (campagnes)
- ✅ Pages visitées et durée
- ✅ Profondeur de scroll
- ✅ Clics sur éléments importants
- ❌ Aucune PII (email, nom, etc.)
- ❌ Ville/adresse précise

---

## 🧪 Vérifier que l'exclusion fonctionne

### 1. Vérifier dans la console du navigateur :
```javascript
// Ouvrez la console (F12)
window.INFPFAnalytics.getState();
```

Vous devriez voir :
```javascript
{
  isExcluded: true,
  excludeReason: "admin_role" // ou "private_path", "dev_cookie"
}
```

### 2. Vérifier dans le dashboard analytics :
1. Allez sur `/admin/analytics`
2. Vérifiez que votre navigation n'apparaît PAS dans les statistiques
3. Si vous voyez vos pages, c'est que l'exclusion n'est pas active

---

## 🔧 Configuration avancée

Éditez `config/analytics_config.php` pour personnaliser :

### Ajouter des routes privées :
```php
'private_paths' => [
    '/^\/admin/',
    '/^\/backoffice/',
    '/^\/mon-espace-prive/',  // Ajoutez ici
],
```

### Ajouter des sous-domaines de test :
```php
'excluded_subdomains' => [
    'dev',
    'staging',
    'preprod',
    'test-monsite',  // Ajoutez ici
],
```

### Créer une whitelist de pages trackées :
```php
'allowed_public_paths' => [
    '/^\/formation/',
    '/^\/metiers/',
    '/^\/blog/',
    '/^\/$/',  // Page d'accueil
],
```

---

## 📊 Commandes utiles

### Vérifier l'état du tracking (console navigateur) :
```javascript
// État complet
window.INFPFAnalytics.getState();

// Activer le mode développeur (bloquer tout)
window.INFPFAnalytics.setDevCookie();

// Désactiver le mode développeur
window.INFPFAnalytics.removeDevCookie();

// Révoquer le consentement
window.INFPFAnalytics.revoke();
```

### Tester en local (serveur PHP) :
```bash
# Voir les logs de tracking
tail -f var/log/dev.log | grep Analytics
```

---

## ✅ Checklist de vérification

- [ ] Connecté en admin → pas de tracking
- [ ] Route `/admin/*` → pas de tracking
- [ ] Cookie `xeilos_dev=1` → pas de tracking
- [ ] "Tout refuser" → aucun cookie, aucune donnée individuelle
- [ ] "Tout accepter" → données collectées, IP anonymisée
- [ ] Dashboard analytics → mon trafic admin n'apparaît pas

---

## 🆘 Support

Si votre trafic apparaît toujours dans les statistiques :

1. Vérifiez que vous êtes bien connecté avec le rôle ADMIN
2. Videz le cache du navigateur (Ctrl+Shift+Delete)
3. Ouvrez la console et tapez : `window.INFPFAnalytics.getState()`
4. Si `isExcluded: false`, utilisez `window.INFPFAnalytics.setDevCookie()`
5. Rechargez la page

Si le problème persiste, vérifiez :
- `config/analytics_config.php` est bien chargé
- Le service `AnalyticsExclusionService` est correctement injecté
- Les logs serveur pour voir si les exclusions sont appliquées

