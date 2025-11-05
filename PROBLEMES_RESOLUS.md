# 🔧 PROBLÈMES RÉSOLUS - Tests et Headers de Sécurité

*Date : 3 novembre 2025*

## 🐛 PROBLÈMES IDENTIFIÉS

### 1. Tests PHPUnit échouent (40 erreurs)
**Erreur** : `You must set the KERNEL_CLASS environment variable`

**Cause** : Configuration PHPUnit incomplète - manque `KERNEL_CLASS` dans `phpunit.xml.dist`

**Solution appliquée** ✅ :
```xml
<server name="KERNEL_CLASS" value="App\Kernel" />
```

Ajout dans `phpunit.xml.dist` et création de `.env.test` pour l'environnement de test.

---

### 2. Headers de Sécurité non appliqués sur dev.infpf.fr
**Score Mozilla Observatory** : 30/100 (D) ❌

**Problèmes détectés** :
- ❌ CSP mal implémenté (-20 points) : `unsafe-inline` présent
- ❌ HSTS non implémenté (-20 points)
- ❌ X-Frame-Options non implémenté (-20 points)
- ❌ X-Content-Type-Options non implémenté (-5 points)
- ❌ SRI non implémenté (-5 points)

**Cause probable** : Les fichiers modifiés localement ne sont pas encore déployés sur `dev.infpf.fr`

**Solution** :
1. ✅ Code déjà corrigé dans le repository local
2. ⏳ **ACTION REQUISE** : Déployer les fichiers suivants sur `dev.infpf.fr` :
   - `src/EventListener/SecurityHeadersListener.php`
   - `config/services.yaml`
   - `config/packages/framework.yaml`
   - `public/.htaccess`
   - `templates/base.html.twig`

---

### 3. CSP trop permissive
**Problème** : Utilisation de `unsafe-inline` et `unsafe-eval`

**Explication** :
- Mozilla Observatory pénalise les CSP avec `unsafe-inline` ou `data:` dans `script-src`
- `unsafe-inline` permet l'exécution de scripts inline (risque XSS)
- `unsafe-eval` permet l'utilisation de `eval()` (risque sécurité)

**Solution actuelle** ✅ :
- Commenté dans le code pour expliquer que c'est temporaire
- `unsafe-inline` conservé pour ne pas casser le site existant
- **Amélioration future** : Remplacer par des nonces pour les scripts inline

---

## ✅ CORRECTIONS APPLIQUÉES

### 1. Configuration PHPUnit

**Fichier** : `phpunit.xml.dist`

**Ajouts** :
```xml
<server name="KERNEL_CLASS" value="App\Kernel" />
```

**Résultat attendu** : Les 40 tests devraient maintenant fonctionner correctement.

---

### 2. Fichier .env.test créé

**Fichier** : `.env.test`

**Contenu** :
```bash
APP_ENV=test
APP_SECRET=TestSecretKey1234567890
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
KERNEL_CLASS='App\Kernel'
```

**But** : Fournir une configuration de test isolée pour PHPUnit.

---

## 🚀 VÉRIFICATIONS À FAIRE

### 1. Tests locaux
```bash
# Tester localement
vendor/bin/phpunit

# Résultat attendu : Tests passent (ou au moins démarrent sans erreur KERNEL_CLASS)
```

---

### 2. Déploiement sur dev.infpf.fr

**Fichiers à déployer** :
```bash
# Sécurité
src/EventListener/SecurityHeadersListener.php
config/services.yaml
config/packages/framework.yaml

# Performance
public/.htaccess
templates/base.html.twig

# Tests
phpunit.xml.dist
.env.test
tests/Service/DataProviderServiceTest.php
tests/EventListener/SecurityHeadersListenerTest.php
tests/Controller/HomeControllerTest.php
```

**Commandes de déploiement** (exemple) :
```bash
# Via Git
git add .
git commit -m "fix: Headers sécurité + tests PHPUnit"
git push origin feature/performance-security-seo-optimization

# Ensuite sur le serveur dev
cd /path/to/dev.infpf.fr
git pull origin feature/performance-security-seo-optimization
composer install
php bin/console cache:clear
```

---

### 3. Vérification des headers sur dev.infpf.fr

**Après déploiement, vérifier** :

```bash
# Test avec curl
curl -I https://dev.infpf.fr

# Devrait afficher :
# X-Frame-Options: DENY
# X-Content-Type-Options: nosniff
# X-XSS-Protection: 1; mode=block
# Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
# Content-Security-Policy: ...
# Referrer-Policy: strict-origin-when-cross-origin
# Permissions-Policy: ...
```

**Outils en ligne** :
- SecurityHeaders.com : https://securityheaders.com/?q=dev.infpf.fr
- Mozilla Observatory : https://observatory.mozilla.org/analyze/dev.infpf.fr

**Score attendu après déploiement** :
- SecurityHeaders.com : **A** ou **A+**
- Mozilla Observatory : **B** ou **A-** (CSP avec unsafe-inline empêche A+)

---

## 📊 RÉSULTATS ACTUELS (PageSpeed Insights)

### Desktop ✅
- Performance : **92/100** ✅
- Accessibilité : **94/100** ✅
- Bonnes pratiques : **96/100** ✅
- SEO : **91/100** ✅

### Mobile ✅
- Performance : **89/100** ✅
- Accessibilité : **92/100** ✅
- Bonnes pratiques : **96/100** ✅
- SEO : **91/100** ✅

**Excellent !** Les optimisations de performance ont fonctionné.

---

## 🎯 PLAN D'ACTION

### Étape 1 : Tester localement ⏳
```bash
vendor/bin/phpunit
```
**Vérifier** : Les tests passent sans erreur KERNEL_CLASS

---

### Étape 2 : Déployer sur dev.infpf.fr ⏳
```bash
# Git push + pull sur serveur dev
git add .
git commit -m "fix: Tests PHPUnit + headers sécurité"
git push

# Sur serveur dev
git pull
composer install
php bin/console cache:clear
```

---

### Étape 3 : Vérifier les headers ⏳
- Tester avec curl ou outils en ligne
- Vérifier SecurityHeaders.com
- Vérifier Mozilla Observatory

---

### Étape 4 : Améliorer la CSP (optionnel) 🔄

**Pour obtenir un score A+ sur Mozilla Observatory** :

1. **Retirer `unsafe-inline`** : Utiliser des nonces
   ```php
   // Générer un nonce unique par requête
   $nonce = base64_encode(random_bytes(16));
   $response->headers->set('X-CSP-Nonce', $nonce);
   
   // CSP avec nonce
   "script-src 'self' 'nonce-{$nonce}' https://ajax.googleapis.com"
   ```

2. **Ajouter nonce aux scripts inline** :
   ```html
   <script nonce="{{ csp_nonce }}">
       // Code inline
   </script>
   ```

3. **SRI (Subresource Integrity)** :
   ```html
   <script src="https://cdn.example.com/script.js" 
           integrity="sha384-..." 
           crossorigin="anonymous"></script>
   ```

---

## 📝 RÉSUMÉ

✅ **Tests PHPUnit** : Configuration corrigée (KERNEL_CLASS ajouté)  
✅ **Headers de Sécurité** : Code prêt (déploiement requis)  
✅ **Performance** : Excellente (92/89 desktop/mobile)  
⏳ **Déploiement** : À faire sur dev.infpf.fr  
🔄 **CSP stricte** : Amélioration future (nonces)  

---

*Corrections appliquées le 3 novembre 2025*











