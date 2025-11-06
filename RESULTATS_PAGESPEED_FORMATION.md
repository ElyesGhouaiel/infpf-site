# 📊 Résultats PageSpeed Insights - Page /formation

**Date** : 3 novembre 2025, 16:32:39  
**URL testée** : https://dev.infpf.fr/formation

---

## 🎯 Scores Globaux

### **📱 MOBILE**
- **Performance** : 65 🟡 (Objectif : 95+)
- **Accessibilité** : 96 ✅
- **Bonnes pratiques** : 96 ✅
- **SEO** : 100 ✅

### **💻 DESKTOP**
- **Performance** : 82 🟡 (Objectif : 95+)
- **Accessibilité** : 95 ✅
- **Bonnes pratiques** : 96 ✅
- **SEO** : 91 ✅

---

## 📉 Core Web Vitals

### **Mobile**
| Métrique | Valeur | Seuil Excellent | Statut |
|----------|--------|-----------------|--------|
| **FCP** (First Contentful Paint) | 3,1 s | < 1,8 s | 🔴 +72% |
| **LCP** (Largest Contentful Paint) | 5,0 s | < 2,5 s | 🔴 +100% |
| **TBT** (Total Blocking Time) | 240 ms | < 200 ms | 🟡 +20% |
| **CLS** (Cumulative Layout Shift) | 0 | < 0,1 | ✅ Excellent |
| **Speed Index** | 6,8 s | < 3,4 s | 🔴 +100% |

### **Desktop**
| Métrique | Valeur | Seuil Excellent | Statut |
|----------|--------|-----------------|--------|
| **FCP** (First Contentful Paint) | 0,7 s | < 1,8 s | ✅ Excellent |
| **LCP** (Largest Contentful Paint) | 1,8 s | < 2,5 s | 🟡 Limite |
| **TBT** (Total Blocking Time) | 100 ms | < 200 ms | ✅ Excellent |
| **CLS** (Cumulative Layout Shift) | 0.005 | < 0,1 | ✅ Excellent |
| **Speed Index** | 4,6 s | < 3,4 s | 🔴 +35% |

---

## 🔴 PROBLÈMES CRITIQUES (Impact Majeur)

### **1. JavaScript Inutilisé : -428 Kio** 🔴🔴🔴
**Impact** : Économie possible de **428 Kio**  
**Cause probable** :
- jQuery chargé en entier (85-90 Kio) mais peu utilisé
- Bootstrap JS complet (70-80 Kio)
- Plugins jQuery non utilisés
- Code mort dans les bundles

**Solution** :
```javascript
// ❌ Actuellement (base.html.twig)
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" defer></script>

// ✅ Option 1 : jQuery Slim (30% plus léger)
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js" defer></script>

// ✅ Option 2 : Vanilla JS (0 Kio)
// Réécrire popup.js sans jQuery
```

**Gain attendu** : +15-20 points Performance

---

### **2. Requêtes de Blocage de l'Affichage** 🔴🔴
**Impact Mobile** : -1870 ms  
**Impact Desktop** : -640 ms

**Cause** :
- CSS chargé de manière synchrone
- JavaScript bloque le rendu initial
- Pas de Critical CSS inline

**Solution** :
```html
<!-- ✅ Critical CSS inline -->
<style>
    /* CSS critique pour le header, hero, etc. */
    .header { ... }
    .formations-list { ... }
</style>

<!-- ✅ CSS non-critique avec preload -->
<link rel="preload" href="{{ asset('styles/main.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="{{ asset('styles/main.css') }}"></noscript>
```

**Gain attendu** : +10-15 points Performance

---

### **3. Speed Index Très Élevé** 🔴🔴
**Mobile** : 6,8 s (objectif < 3,4 s)  
**Desktop** : 4,6 s (objectif < 3,4 s)

**Cause** :
- Rendu progressif bloqué par JS/CSS
- Images non optimisées
- Pas de lazy loading efficace

**Solution** :
- Inline Critical CSS
- Defer tous les JS non-critiques
- Lazy loading agressif des images

**Gain attendu** : +15-20 points Performance

---

### **4. LCP (Largest Contentful Paint) Élevé** 🔴
**Mobile** : 5,0 s (objectif < 2,5 s)  
**Desktop** : 1,8 s (objectif < 2,5 s - limite)

**Cause** :
- Image héro ou header trop lourde
- Détection LCP tardive
- Pas de preload de l'élément LCP

**Solution** :
```html
<!-- ✅ Preload de l'image LCP -->
<link rel="preload" as="image" href="{{ asset('img/hero-formation.jpg') }}">

<!-- ✅ Image avec fetchpriority -->
<img src="hero.jpg" fetchpriority="high" loading="eager">
```

**Gain attendu** : +10 points Performance

---

## 🟡 PROBLÈMES MOYENS (Impact Modéré)

### **5. CSS Inutilisé : -55 Kio** 🟡
**Impact** : Économie possible de **55 Kio**

**Cause** :
- Bootstrap chargé en entier
- Classes CSS non utilisées sur cette page

**Solution** :
```bash
# Purger CSS inutilisé avec PurgeCSS
npm install -g purgecss
purgecss --css public/styles/*.css --content templates/**/*.twig --output public/styles/purged/
```

**Gain attendu** : +5 points Performance

---

### **6. Réduire CSS : -15 Kio** 🟡
**Impact** : Économie possible de **15 Kio**

**Solution** :
```yaml
# config/packages/webpack_encore.yaml (si utilisé)
Encore.enablePostCssLoader()
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableVersioning(Encore.isProduction())
```

**Gain attendu** : +3 points Performance

---

### **7. Réduire JavaScript : -3 Kio** 🟡
**Impact** : Économie possible de **3 Kio**

**Solution** :
- Minification avec Terser
- Uglify des scripts personnalisés

**Gain attendu** : +2 points Performance

---

### **8. Images Non Optimisées : -126 Kio (Desktop) / -53 Kio (Mobile)** 🟡
**Impact** : Économie possible de **53-126 Kio**

**Problème** :
- Pas de format WebP
- Pas de dimensions explicites (width/height)
- Images trop lourdes

**Solution** :
```twig
{# ✅ Images avec dimensions explicites + WebP #}
<picture>
    <source srcset="{{ asset('img/formations/formation-1.webp') }}" type="image/webp">
    <img 
        src="{{ asset('img/formations/formation-1.jpg') }}" 
        alt="Formation" 
        width="400" 
        height="300" 
        loading="lazy"
    >
</picture>
```

**Gain attendu** : +5-8 points Performance

---

### **9. Cache Headers : -11 Kio** 🟡
**Impact** : Économie possible de **11 Kio**

**Problème** :
- Durées de cache trop courtes
- Pas de cache pour certaines ressources

**Solution** :
```apache
# public/.htaccess - DÉJÀ EN PLACE ✅
<FilesMatch "\.(ico|png|jpg|jpeg|gif|webp|svg)$">
    Header set Cache-Control "public, max-age=31536000, immutable"
</FilesMatch>
```

**Vérifier que les headers sont bien appliqués** :
```bash
curl -I https://dev.infpf.fr/img/logo.png | grep -i "cache-control"
```

**Gain attendu** : +2 points Performance

---

### **10. Affichage Police : -10 ms** 🟡
**Impact** : Économie possible de **10 ms**

**Solution** :
```html
<!-- ✅ Preconnect + font-display -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
```

**Gain attendu** : +1-2 points Performance

---

## 🟢 POINTS FORTS (À Conserver)

### **1. CLS (Cumulative Layout Shift) : 0** ✅
Excellent ! Pas de décalage de mise en page.

### **2. Accessibilité : 95-96** ✅
Très bon score.

### **3. Bonnes pratiques : 96** ✅
Excellentes pratiques générales.

### **4. SEO : 91-100** ✅
Bon référencement.

---

## 🎯 PLAN D'ACTION PAR PRIORITÉ

### **PRIORITÉ 1 : JavaScript** (Gain : +15-20 pts)
1. ✅ Remplacer jQuery complet par jQuery Slim
2. ✅ Analyser et supprimer JavaScript inutilisé
3. ✅ Réécrire popup.js en Vanilla JS si possible
4. ✅ Defer tous les scripts non-critiques

### **PRIORITÉ 2 : CSS Critique** (Gain : +10-15 pts)
1. ✅ Extraire et inline le CSS critique
2. ✅ Charger le reste du CSS de manière asynchrone
3. ✅ Purger CSS inutilisé avec PurgeCSS

### **PRIORITÉ 3 : Images** (Gain : +5-8 pts)
1. ✅ Convertir images en WebP
2. ✅ Ajouter width/height explicites
3. ✅ Preload image LCP

### **PRIORITÉ 4 : LCP Optimization** (Gain : +8-10 pts)
1. ✅ Identifier l'élément LCP (probablement image hero)
2. ✅ Preload cet élément
3. ✅ fetchpriority="high" sur l'élément LCP

### **PRIORITÉ 5 : Base de Données** (Gain : +5 pts)
1. ✅ Pagination SQL (5 formations au lieu de 48)
2. ✅ Eager loading (éviter N+1)
3. ✅ Cache des compteurs

---

## 📈 GAINS ATTENDUS

| Optimisation | Mobile (Avant) | Desktop (Avant) | Mobile (Après) | Desktop (Après) | Gain |
|--------------|----------------|-----------------|----------------|-----------------|------|
| **JavaScript** | 65 | 82 | 80 | 92 | +10-15 pts |
| **CSS Critique** | 80 | 92 | 88 | 96 | +8 pts |
| **Images** | 88 | 96 | 92 | 98 | +4 pts |
| **LCP** | 92 | 98 | 95 | 99 | +3 pts |
| **BDD** | 95 | 99 | **97** | **100** | +2 pts |

---

## 🚀 OBJECTIFS FINAUX

| Format | Avant | Objectif | Stratégie |
|--------|-------|----------|-----------|
| **Mobile** | 65 | **95+** | JS + CSS Critique + Images |
| **Desktop** | 82 | **98+** | JS + CSS Critique + BDD |

---

## 🛠️ PROCHAINES ÉTAPES

### **Étape 1 : JavaScript** (30 min)
```bash
# Remplacer jQuery par jQuery Slim
# Analyser popup.js
# Defer tous les scripts
```

### **Étape 2 : CSS Critique** (45 min)
```bash
# Extraire Critical CSS
# Inline dans base.html.twig
# Async le reste
```

### **Étape 3 : Images** (1h)
```bash
# Conversion WebP
# Ajouter dimensions
# Preload LCP
```

### **Étape 4 : BDD** (1h)
```bash
# Pagination SQL
# Eager loading
# Cache
```

---

**Temps total estimé** : 3-4 heures  
**Gains attendus** : **Mobile 65 → 95+ (+30 pts)** | **Desktop 82 → 98+ (+16 pts)**

---

**Date de création** : 3 novembre 2025  
**Statut** : 🚀 Prêt à commencer les optimisations










