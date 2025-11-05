# 🏠 OPTIMISATIONS PAGE D'ACCUEIL - 04/11/2025

## 📊 Scores Avant Optimisations

| Version | Performance | FCP | LCP | TBT | CLS |
|---|---|---|---|---|---|
| **Mobile** | 85/100 | 2.5s | 2.7s | 280ms | 0.023 |
| **Desktop** | 92/100 | 0.7s | 1.2s | 120ms | 0 |

---

## ✅ Optimisations Appliquées

### 1. ❌ **JavaScript Inutilisé : -30.8 Ko**

**Problème** : jQuery chargé mais non utilisé

**Solution** :
```diff
- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
+ {# jQuery supprimé - non utilisé sur cette page (économie de 30.8 Kio) #}
```

**Économie** : **30.8 Ko** + **réduction du temps de parsing JavaScript**

---

### 2. ⚡ **Requêtes Bloquantes : -1240ms (mobile) / -310ms (desktop)**

**Problème** : AOS.css et AOS.js bloquent le rendu initial

**Solutions** :

#### A. AOS.css - Preload asynchrone
```diff
- <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
+ <link rel="preload" href="https://unpkg.com/aos@2.3.1/dist/aos.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
+ <noscript><link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"></noscript>
```

#### B. AOS.js - Defer
```diff
- <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
+ <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
```

#### C. Initialisation AOS - DOMContentLoaded
```diff
- <script>
-   AOS.init({
+ <script defer>
+   document.addEventListener('DOMContentLoaded', function() {
+     if (typeof AOS !== 'undefined') {
+       AOS.init({
+         duration: 800,
+         easing: 'ease-in-out',
+         once: true,
+         offset: 50
+       });
+     }
+   });
```

**Gain estimé** : **-1240ms** sur mobile, **-310ms** sur desktop

---

### 3. 🖼️ **Images : Dimensions Explicites + Lazy Loading**

**Problème** : 4 images sans `width`/`height` explicites causant du CLS

**Solutions** :

#### A. Images de catégories
```diff
<img 
  src="{{ asset('img/' ~ categoryImages[category.id] ?? 'default-image.jpg') }}" 
  alt="{{ category.name }}"
  class="category-image"
+ width="300"
+ height="200"
+ loading="lazy"
>
```

#### B. Image CPF
```diff
- <img src="{{ asset('img/CPF.png') }}" class="wp-image-cpf_lady" alt="formation_financable_cpf">
+ <img src="{{ asset('img/CPF.png') }}" class="wp-image-cpf_lady" alt="formation_financable_cpf" width="940" height="788" loading="lazy">
```

#### C. Images de blog
```diff
- <img src="/uploads/images/{{ blog.image }}" class="card-img-top" alt="Image description">
+ <img src="/uploads/images/{{ blog.image }}" class="card-img-top" alt="Image description" width="400" height="250" loading="lazy">
```

**Gain estimé** : 
- **Réduction CLS** : 0.023 → ~0.01
- **Lazy loading** : économie de **116-675 Ko** sur chargement initial

---

## 📈 Gains Attendus

| Métrique | Avant | Après (estimé) | Gain |
|---|---|---|---|
| **Performance Mobile** | 85 | **90-95** | +5-10 |
| **FCP Mobile** | 2.5s | **1.5s** | -1.0s |
| **LCP Mobile** | 2.7s | **2.0s** | -0.7s |
| **TBT Mobile** | 280ms | **150ms** | -130ms |
| **CLS Mobile** | 0.023 | **0.01** | -0.013 |
| **Performance Desktop** | 92 | **95-98** | +3-6 |

---

## ⚠️ Optimisation Restante

### CSS Inutilisé : 84-88 Ko

**Problème** : Fichiers CSS contiennent des règles non utilisées sur la page d'accueil

**Solution possible** :
- Utiliser PurgeCSS pour supprimer le CSS inutilisé
- Créer un CSS spécifique pour la page d'accueil
- Utiliser Critical CSS inline

**Note** : Cette optimisation nécessite une analyse approfondie pour ne pas casser le design. À faire ultérieurement si le score n'est pas satisfaisant.

---

## 🧪 Test à Effectuer

### 1. Vérifier visuellement
- ✅ Images chargent correctement
- ✅ Animations AOS fonctionnent
- ✅ Pas de régression visuelle

### 2. Tester avec Lighthouse
```
https://pagespeed.web.dev/analysis?url=https://dev.infpf.fr
```

**Objectif** : 
- Mobile : **90+/100**
- Desktop : **95+/100**

---

## 📝 Fichiers Modifiés

- `templates/home/home.html.twig` : Suppression jQuery, defer AOS, dimensions images

---

## 🚀 Déploiement

**Environnement** : `dev.infpf.fr` (branche `feature/performance-security-seo-optimization`)

**Actions effectuées** :
1. ✅ Modifications template
2. ✅ Cache Symfony vidé
3. ⏳ Test Lighthouse à effectuer

**Pour déployer en production** :
1. Tester sur `dev.infpf.fr`
2. Si scores OK, merger vers `main`
3. Purger CDN Hostinger

---

## 💡 Notes Techniques

### Pourquoi defer sur AOS.js ?
- AOS n'est nécessaire que pour les animations scroll
- Le defer permet de ne pas bloquer le rendu initial
- Les animations ne sont visibles qu'après scroll donc pas critique pour FCP/LCP

### Pourquoi preload + onload sur AOS.css ?
- Technique standard pour charger CSS de manière asynchrone
- Évite le blocage du rendu initial
- Le `onload="this.onload=null;this.rel='stylesheet'"` transforme le preload en stylesheet une fois chargé

### Dimensions images
- Les dimensions explicites permettent au navigateur de réserver l'espace avant le chargement
- Réduit le CLS (Cumulative Layout Shift)
- Compatible avec `object-fit: cover` en CSS

---

**🎉 PAGE D'ACCUEIL OPTIMISÉE !**

**Date** : 04 Novembre 2025  
**Auteur** : Assistant IA  
**Branche** : `feature/performance-security-seo-optimization`







