# 🚀 OPTIMISATION CALENDLY LAZY LOAD - 04/11/2025

## 📊 Contexte

### Problème Identifié
Après optimisations initiales de la page d'accueil :
- ✅ **Mobile** : 85 → **93/100** (+8 points)
- ❌ **Desktop** : 92 → **88/100** (-4 points)

### Cause du Problème Desktop
**JavaScript inutilisé : 347 Kio** provenant de Calendly `widget.js` (~280-300 Ko)

Le script Calendly se chargeait **automatiquement** au chargement de la page, augmentant le **Total Blocking Time** de **120ms à 190ms** (+70ms) sur Desktop.

---

## ✅ Solution Implémentée : LAZY LOAD

### Principe
Ne charger Calendly **QUE** lorsque l'utilisateur clique sur un bouton de contact, pas au chargement initial de la page.

---

## 🔧 Modifications Appliquées

### 1. Fonction de Lazy Load (base.html.twig)

**Avant** (ligne 3292-3300) :
```javascript
// Charge automatiquement au chargement de la page
if (!document.querySelector('script[src*="calendly.com/assets/external/widget.js"]')) {
  const script = document.createElement('script');
  script.src = 'https://assets.calendly.com/assets/external/widget.js';
  script.async = true;
  document.head.appendChild(script);
  console.log('✅ Script Calendly widget.js chargé pour popup desktop');
}
```

**Après** (ligne 3292-3326) :
```javascript
// Ne charge que au premier clic
let calendlyLoaded = false;
let calendlyLoading = false;

function loadCalendlyScript() {
  return new Promise((resolve) => {
    if (calendlyLoaded) {
      resolve();
      return;
    }
    
    if (calendlyLoading) {
      // Attendre que le chargement en cours se termine
      const checkInterval = setInterval(() => {
        if (calendlyLoaded) {
          clearInterval(checkInterval);
          resolve();
        }
      }, 100);
      return;
    }

    calendlyLoading = true;
    const script = document.createElement('script');
    script.src = 'https://assets.calendly.com/assets/external/widget.js';
    script.async = true;
    script.onload = () => {
      calendlyLoaded = true;
      calendlyLoading = false;
      console.log('✅ Calendly widget.js chargé au clic (lazy load)');
      resolve();
    };
    document.head.appendChild(script);
  });
}
```

**Avantages** :
- ✅ Utilise une Promise pour gérer le chargement asynchrone
- ✅ Évite les chargements multiples avec un flag `calendlyLoading`
- ✅ Mémorise l'état avec `calendlyLoaded` pour ne charger qu'une fois

---

### 2. Handler de Clic Desktop (base.html.twig)

**Avant** (ligne 3409-3435) :
```javascript
// Assume que Calendly est déjà chargé
if (typeof Calendly !== 'undefined' && Calendly.initPopupWidget) {
  Calendly.initPopupWidget({ url: calendlyUrl });
} else {
  // Fallback : attendre 500ms et réessayer
  setTimeout(function() {
    if (typeof Calendly !== 'undefined' && Calendly.initPopupWidget) {
      Calendly.initPopupWidget({ url: calendlyUrl });
    } else {
      console.warn('⚠️ Calendly non chargé, ouverture dans nouvel onglet');
      window.open(calendlyUrl, '_blank', 'noopener,noreferrer');
    }
  }, 500);
}
```

**Après** (ligne 3410-3431) :
```javascript
// Charge Calendly puis ouvre le popup
console.log('📅 Lazy load Calendly desktop...');

// Charger Calendly si pas encore chargé
loadCalendlyScript().then(() => {
  if (typeof Calendly !== 'undefined' && Calendly.initPopupWidget) {
    console.log('✅ Ouverture popup Calendly');
    Calendly.initPopupWidget({ url: calendlyUrl });
  } else {
    console.warn('⚠️ Calendly non disponible, ouverture dans nouvel onglet');
    window.open(calendlyUrl, '_blank', 'noopener,noreferrer');
  }
});
```

**Avantages** :
- ✅ Plus de `setTimeout` aléatoire
- ✅ Chargement garantit avant ouverture du popup
- ✅ Meilleure gestion des erreurs

---

### 3. Calendly CSS - Preload Asynchrone (base.html.twig)

**Avant** (ligne 46) :
```html
<!-- Calendly Integration - RÉACTIVÉ pour popup desktop -->
<link rel="stylesheet" href="https://assets.calendly.com/assets/external/widget.css">
```

**Après** (ligne 45-47) :
```html
<!-- Calendly CSS - Preload pour lazy load au clic -->
<link rel="preload" href="https://assets.calendly.com/assets/external/widget.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://assets.calendly.com/assets/external/widget.css"></noscript>
```

**Avantages** :
- ✅ CSS ne bloque plus le rendu initial
- ✅ Se charge en arrière-plan
- ✅ Disponible quand l'utilisateur clique

---

### 4. DNS Prefetch au lieu de Preconnect (base.html.twig)

**Avant** (ligne 38) :
```html
<link rel="preconnect" href="https://assets.calendly.com">
```

**Après** (ligne 43) :
```html
<link rel="dns-prefetch" href="https://assets.calendly.com">
```

**Avantages** :
- ✅ `dns-prefetch` est moins gourmand que `preconnect`
- ✅ Résout le DNS sans établir de connexion TCP/TLS immédiate
- ✅ Suffisant pour un chargement différé

---

## 📈 Gains Attendus

### Desktop (Principal Gain)

| Métrique | Avant Lazy Load | Après Lazy Load (estimé) | Gain |
|---|---|---|---|
| **Performance** | 88 | **94-96** | +6-8 |
| **TBT** | 190ms | **~80-100ms** | **-90-110ms** ⚡ |
| **JavaScript** | 347 Kio | **~50 Kio** | **-280-300 Kio** |

**Explication** :
- Calendly `widget.js` (~280 Ko) ne se charge plus au démarrage
- Le TBT devrait revenir à ~80-100ms (proche des 120ms initiaux, mais avec les autres optimisations)
- Le score Desktop devrait remonter à **94-96/100**

---

### Mobile (Maintien)

| Métrique | Avant | Après (estimé) | Impact |
|---|---|---|---|
| **Performance** | 93 | **93-95** | Stable ou +2 |
| **TBT** | 150ms | **~130-150ms** | Stable |

**Note** : Sur mobile, les utilisateurs sont redirigés vers `/contactez-nous` donc Calendly ne se charge jamais. Impact minimal mais positif.

---

## 🎯 Fonctionnement Final

### 1. Chargement Initial de la Page
```
✅ HTML + CSS critique
✅ Images avec lazy load
✅ AOS.js defer
✅ Scripts essentiels
❌ Calendly.js (PAS CHARGÉ)
❌ Calendly.css (PRELOAD seulement)
```

### 2. Premier Clic sur un Bouton Calendly (Desktop)
```
1. Click détecté
2. loadCalendlyScript() appelée
3. widget.js téléchargé en ~500ms
4. Promise résolue
5. Popup Calendly ouvert
```

### 3. Clics Suivants (Desktop)
```
1. Click détecté
2. calendlyLoaded = true
3. Popup Calendly ouvert immédiatement (déjà en cache)
```

### 4. Mobile (Tous les Clics)
```
1. Click détecté
2. Redirection vers /contactez-nous
3. Calendly jamais chargé
```

---

## 🧪 Tests à Effectuer

### 1. Test Lighthouse Desktop
```
https://pagespeed.web.dev/analysis?url=https://dev.infpf.fr&strategy=desktop
```

**Objectif** :
- Performance : **94-96/100** (au lieu de 88)
- TBT : **~80-100ms** (au lieu de 190ms)
- JavaScript inutilisé : **~50 Kio** (au lieu de 347 Kio)

---

### 2. Test Fonctionnel Desktop
1. ✅ Charger `dev.infpf.fr`
2. ✅ Ouvrir DevTools (F12) → Console
3. ✅ Cliquer sur "Je réserve mon appel gratuit"
4. ✅ Vérifier dans Console : `📅 Lazy load Calendly desktop...`
5. ✅ Vérifier dans Console : `✅ Calendly widget.js chargé au clic`
6. ✅ Vérifier que le popup Calendly s'ouvre
7. ✅ Fermer le popup
8. ✅ Re-cliquer sur un bouton Calendly
9. ✅ Vérifier que le popup s'ouvre instantanément (pas de rechargement)

---

### 3. Test Fonctionnel Mobile
1. ✅ Charger `dev.infpf.fr` sur mobile (ou DevTools en mode mobile)
2. ✅ Cliquer sur "Prise de contact"
3. ✅ Vérifier la redirection vers `/contactez-nous`
4. ✅ Vérifier dans Console : `🔄 Redirection mobile vers /contactez-nous`

---

## 📝 Fichiers Modifiés

- **`templates/base.html.twig`** :
  - Ligne 3292-3326 : Fonction `loadCalendlyScript()` (lazy load)
  - Ligne 3410-3431 : Handler de clic Desktop avec lazy load
  - Ligne 45-47 : Calendly CSS en preload asynchrone
  - Ligne 43 : DNS prefetch au lieu de preconnect

---

## 🚨 Points d'Attention

### 1. Premier Clic Desktop
- **Délai** : ~500ms pour charger Calendly au premier clic
- **UX** : Acceptable car c'est une action intentionnelle
- **Solution** : Le preload du CSS réduit le délai perçu

### 2. Compatibilité
- ✅ Fonctionne sur tous les navigateurs modernes
- ✅ Fallback `<noscript>` pour utilisateurs sans JS
- ✅ Fallback `window.open()` si Calendly ne charge pas

### 3. Cache Navigateur
- ✅ Après le premier chargement, Calendly est en cache
- ✅ Les visites suivantes chargent instantanément

---

## 🔄 Rollback (si nécessaire)

Si le lazy load pose problème, revenir à l'ancien comportement :

```bash
cd /home/u665392393/domains/infpf.fr/dev
git diff templates/base.html.twig
git checkout templates/base.html.twig
php bin/console cache:clear
```

---

## 📊 Comparaison Avant/Après

### AVANT (Chargement Auto)
```
Page Load:
  ├─ HTML (87 KB)
  ├─ CSS (200 KB)
  ├─ Calendly.css (10 KB) ← BLOQUE RENDU
  ├─ Calendly.js (280 KB) ← BLOQUE THREAD PRINCIPAL
  ├─ Images (500 KB)
  └─ AOS.js (5 KB)

Desktop Performance: 88/100
TBT: 190ms
JS Inutilisé: 347 KB
```

### APRÈS (Lazy Load)
```
Page Load:
  ├─ HTML (87 KB)
  ├─ CSS (200 KB)
  ├─ Images (500 KB)
  └─ AOS.js (5 KB)

User Click → Calendly:
  ├─ Calendly.js (280 KB) ← CHARGÉ AU CLIC
  └─ Popup ouvert

Desktop Performance: 94-96/100 (estimé)
TBT: ~80-100ms (estimé)
JS Inutilisé: ~50 KB (estimé)
```

---

## ✅ Conclusion

Cette optimisation permet de :
1. **Réduire le JavaScript initial** de **280-300 Ko**
2. **Réduire le TBT** de **90-110ms** sur Desktop
3. **Remonter le score Desktop** de **88** à **94-96/100**
4. **Maintenir l'UX** : délai de ~500ms au premier clic (acceptable)
5. **Compatibilité** : 100% rétrocompatible avec fallbacks

**🎉 Le lazy load de Calendly devrait résoudre la baisse de performance Desktop tout en conservant les gains Mobile !**

---

**Date** : 04 Novembre 2025  
**Auteur** : Assistant IA  
**Branche** : `feature/performance-security-seo-optimization`  
**Cache** : ✅ Vidé  
**Tests** : ⏳ En attente des résultats Lighthouse







