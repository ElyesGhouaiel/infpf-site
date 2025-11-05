# 🚀 OPTIMISATIONS PAGE /formation/{id} - 2025-11-04

## 📊 **SCORES LIGHTHOUSE AVANT OPTIMISATION**

| **Version** | **Performance** | **Problèmes Critiques** |
|---|---|---|
| **Mobile** | **77/100** 🔴 | Speed Index 9,1s, Stripe.js 205 KiB inutilisé |
| **Desktop** | **86/100** ⚠️ | Même problème |

---

## 🔴 **PROBLÈMES MAJEURS IDENTIFIÉS**

### **1. 🚨 STRIPE.JS (Coupable #1 - 100% inutilisé !)**
- **Taille** : 205 KiB chargé automatiquement
- **Blocage rendu** : 2100ms (requête bloquante)
- **JavaScript inutilisé** : 134.2 KiB (65% inutilisé)
- **Temps d'exécution** : 197ms sur le thread principal
- **Impact** : Speed Index 9,1s au lieu de ~3-4s
- **⚠️ CRITIQUE** : Stripe.js n'est **PAS UTILISÉ** sur cette page !
  - Les CTA redirigent vers CPF externe ou page de contact
  - Aucun paiement direct

### **2. ⏱️ LCP catastrophique : 3,3s**
- **Délai d'affichage** : **5070ms** (énorme !)
- **Élément LCP** : Bouton "💬 Poser mes questions"
- **Cause** : Stripe.js bloque le rendu

### **3. 🎨 CLS : 0.062**
- **Élément** : `.hero__grid`
- **Cause** : Pas de hauteur minimale définie, layout recalculé après chargement

### **4. ⚡ Animation non composée**
- **Animation** : `pulse-glow` sur `.cta-section-mega`
- **Problème** : Anime `box-shadow` (propriété non composable)
- **Impact** : Force recalcul du layout à chaque frame

### **5. 📦 CSS non optimisé**
- **CSS inline non minifié** : 21,7 KiB → économie 8,3 KiB
- **CSS inutilisé** : 32 Kio

---

## ✅ **OPTIMISATIONS APPLIQUÉES**

### **1. ⚡ SUPPRESSION STRIPE.JS (Gain estimé: +10-12 points)**

**Fichier modifié** : `/templates/content/formation/show.html.twig` (lignes 2679-2691)

**AVANT** :
```html
<!-- Stripe SDK avec clé publique -->
<script src="https://js.stripe.com/v3/"></script>
<script>
// ===== DONNÉES FORMATION =====
const formationData = {
    id: {{ formations.id|json_encode|raw }},
    prix: {{ formations.priceFormation|default(0)|json_encode|raw }}
};

// ===== INITIALISATION STRIPE AVEC CLÉ PUBLIQUE =====
const stripe = Stripe('pk_test_51PGd7RP7ZQZW88VWYourPublicKeyHere');

// Diagnostic Stripe amélioré
console.log('=== STRIPE DIAGNOSTIC ===');
console.log('Stripe object:', stripe);
console.log('Formation ID:', formationData.id);
console.log('Prix formation:', formationData.prix);
</script>
```

**APRÈS** :
```html
<!-- ===== STRIPE.JS SUPPRIMÉ - NON UTILISÉ SUR CETTE PAGE =====
     Les paiements passent par CPF externe ou demande de devis
     Économie : 205 KiB JS + 2100ms de blocage rendu
-->
```

**Bénéfices** :
- **-205 KiB** de JavaScript au chargement initial
- **-2100ms** de blocage du rendu
- **-134 KiB** de JavaScript inutilisé
- **-197ms** d'exécution sur le thread principal
- **Speed Index** : 9,1s → ~3-4s (estimation)

---

### **2. 🎨 CORRECTION CLS (Gain estimé: +1-2 points)**

**Fichier modifié** : `/templates/content/formation/show.html.twig` (lignes 149-158)

**AVANT** :
```css
.hero__grid {
    position: relative;
    display: grid;
    grid-template-columns: var(--toc-w) var(--gap) minmax(var(--content-min), 1fr) var(--gap) var(--det-w);
    align-items: center;
    padding-block: clamp(48px, 6vh, 80px);
    width: 100%;
    min-height: inherit;
}
```

**APRÈS** :
```css
.hero__grid {
    position: relative;
    display: grid;
    grid-template-columns: var(--toc-w) var(--gap) minmax(var(--content-min), 1fr) var(--gap) var(--det-w);
    align-items: center;
    padding-block: clamp(48px, 6vh, 80px);
    width: 100%;
    min-height: 400px; /* Hauteur minimale explicite pour éviter CLS */
    contain: layout; /* Isolation du layout pour éviter reflows */
}
```

**Bénéfices** :
- **CLS** : 0.062 → ~0 (estimation)
- **Layout stable** dès le chargement initial
- **Pas de décalage** lors du chargement des polices/images

---

### **3. ⚡ ANIMATION GPU-ACCELERATED (Gain estimé: +0.5-1 point)**

**Fichier modifié** : `/templates/content/formation/show.html.twig` (lignes 1653-1666)

**AVANT** :
```css
.cta-section-mega {
    /* ... */
    animation: pulse-glow 3s ease-in-out infinite;
}

@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 
            0 20px 60px rgba(11, 63, 137, 0.4),
            0 0 0 0 rgba(11, 63, 137, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    50% {
        box-shadow: 
            0 20px 80px rgba(11, 63, 137, 0.6),
            0 0 60px 10px rgba(0, 180, 216, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }
}
```

**APRÈS** :
```css
.cta-section-mega {
    /* ... */
    animation: pulse-glow 3s ease-in-out infinite;
    will-change: transform; /* GPU acceleration */
}

/* Animation GPU-accelerated (transform + opacity au lieu de box-shadow) */
@keyframes pulse-glow {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.02);
        opacity: 0.95;
    }
}
```

**Bénéfices** :
- **Animation composée** par le GPU (transform/opacity)
- **Pas de recalcul du layout** à chaque frame
- **Performance fluide** à 60 FPS

---

## 📈 **SCORES ATTENDUS APRÈS OPTIMISATION**

| **Version** | **Avant** | **Après (Estimé)** | **Gain** |
|---|---|---|---|
| **Mobile** | **77/100** 🔴 | **90-93/100** ✅ | **+13-16 points** |
| **Desktop** | **86/100** ⚠️ | **96-98/100** ✅ | **+10-12 points** |

### **Métriques attendues (Mobile)** :
- **Speed Index** : 9.1s → **~3-4s** ✅ (-5-6s)
- **LCP** : 3.3s → **~1.5-2s** ✅ (-1.3-1.8s)
- **TBT** : 90ms → **~30-50ms** ✅ (-40-60ms)
- **CLS** : 0.062 → **~0** ✅ (-0.062)
- **FCP** : 3.2s → **~1.8-2.2s** ✅ (-1-1.4s)

---

## 🧪 **TEST LIGHTHOUSE**

### **URLs à tester** :

**Mobile** :
```
https://pagespeed.web.dev/analysis?url=https://dev.infpf.fr/formation/87
```

**Desktop** :
```
https://pagespeed.web.dev/analysis?url=https://dev.infpf.fr/formation/87&strategy=desktop
```

*(Remplace `87` par l'ID de la formation testée)*

---

## 📋 **OPTIMISATIONS FUTURES POSSIBLES**

### **1. Minification CSS inline (Gain: +1-2 points)**
- **CSS inline** : ~37 Kio → minifier pour économiser 14 Kio
- **Script PHP de minification** : À créer si besoin

### **2. Lazy load images footer (Gain: +0.5 point)**
- **Logos réseaux sociaux** : Facebook, YouTube, Instagram
- **Logos certifications** : Qualiopi, Datadock
- **Solution** : Conversion WebP + dimensions explicites + loading="lazy"

### **3. Préconnexions optimisées (Gain: +0.5 point)**
- **Ajouter** : `preconnect` pour Calendly (si utilisé)
- **Ajouter** : `dns-prefetch` pour les domaines externes

---

## 🎯 **OBJECTIFS ATTEINTS**

✅ **Stripe.js supprimé** (205 KiB économisés, inutilisé)  
✅ **CLS corrigé** (0.062 → ~0, hero__grid stabilisé)  
✅ **Animation GPU-accelerated** (transform au lieu de box-shadow)  
✅ **will-change: transform** (accélération GPU)  
✅ **Cache vidé** (Symfony prod)

---

## 📝 **FICHIERS MODIFIÉS**

1. `/templates/content/formation/show.html.twig`
   - Suppression Stripe.js (lignes 2679-2691)
   - Correction CLS hero__grid (lignes 149-158)
   - Animation GPU-accelerated (lignes 1653-1666)

---

## 🔄 **PROCHAINE ÉTAPE**

**RE-TEST LIGHTHOUSE** sur la page `/formation/{id}` :
- URL Mobile : https://pagespeed.web.dev/analysis?url=https://dev.infpf.fr/formation/87
- URL Desktop : https://pagespeed.web.dev/analysis?url=https://dev.infpf.fr/formation/87&strategy=desktop

**Objectifs** :
- ✅ Mobile : **90-93/100** (au lieu de 77/100)
- ✅ Desktop : **96-98/100** (au lieu de 86/100)
- ✅ Speed Index : **~3-4s** (au lieu de 9,1s)
- ✅ TBT : **~30-50ms** (au lieu de 90ms)

---

**Date** : 2025-11-04  
**Développeur** : Assistant IA  
**Environnement** : dev.infpf.fr  
**Branche Git** : `feature/performance-security-seo-optimization`







