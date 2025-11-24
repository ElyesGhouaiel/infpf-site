#  OPTIMISATIONS PAGE /formation - 2025-11-04

##  **SCORES LIGHTHOUSE AVANT OPTIMISATION**

| **Version** | **Performance** | **Problèmes Identifiés** |
|---|---|---|
| **Mobile** | **89/100**  | TBT 240ms, JavaScript inutilisé 348 Kio |
| **Desktop** | **98/100**  | Bon score mais JavaScript bloquant |

---

##  **PROBLÈMES PRINCIPAUX IDENTIFIÉS**

### **1. Google reCAPTCHA (Principal coupable)**
- **Impact** : 690 Kio de JavaScript chargés automatiquement
- **Tâches longues** : 668ms (bloque le thread principal)
- **JavaScript inutilisé** : 347.6 Kio (99% du JS inutilisé)
- **Source** : `templates/components/modal.html.twig` (chargé automatiquement au page load)

### **2. Logo LCP non optimisé**
- **Élément LCP** : `<img class="logo-infpf" ... CROPPED_LOGO__INFPF_2_150.webp>`
- **LCP** : 2,3s
- **Manque** : `fetchpriority="high"` et `loading="eager"`

### **3. Préconnexions non optimales**
- Google/gstatic connectés inutilement (reCAPTCHA non utilisé immédiatement)
- unpkg.com en `dns-prefetch` au lieu de `preconnect` (AOS.js utilisé immédiatement)

---

##  **OPTIMISATIONS APPLIQUÉES**

### **1.  LAZY LOAD reCAPTCHA (Gain estimé: +6-8 points mobile)**

**Fichier modifié** : `/templates/components/modal.html.twig`

**Changement** : reCAPTCHA ne se charge plus automatiquement au chargement de la page, mais **uniquement** lorsque :
- L'utilisateur clique sur le bouton "Demande de documentation"
- La modal s'ouvre (détecté via MutationObserver)

**Code implémenté** :
```javascript
// Variable globale pour tracker le chargement de reCAPTCHA
let recaptchaLoadedForModal = false;
let recaptchaLoadingForModal = false;

// Fonction pour charger reCAPTCHA de manière asynchrone
function loadRecaptchaForModal() {
    return new Promise((resolve) => {
        if (recaptchaLoadedForModal) {
            resolve();
            return;
        }
        
        if (recaptchaLoadingForModal) {
            // Attendre que le chargement en cours se termine
            const checkInterval = setInterval(() => {
                if (recaptchaLoadedForModal) {
                    clearInterval(checkInterval);
                    resolve();
                }
            }, 100);
            return;
        }
        
        // Ne charger que si pas déjà présent
        if (document.querySelector('script[src*="recaptcha"]')) {
            recaptchaLoadedForModal = true;
            resolve();
            return;
        }
        
        recaptchaLoadingForModal = true;
        console.log(' Lazy load reCAPTCHA pour modal...');
        
        const recaptchaScript = document.createElement('script');
        recaptchaScript.src = 'https://www.google.com/recaptcha/api.js?render=6Led...';
        recaptchaScript.async = true;
        recaptchaScript.onload = () => {
            recaptchaLoadedForModal = true;
            recaptchaLoadingForModal = false;
            console.log(' reCAPTCHA chargé pour modal');
            resolve();
        };
        document.head.appendChild(recaptchaScript);
    });
}
```

**Bénéfices** :
- **-690 Kio** de JavaScript au chargement initial
- **-668ms** de tâches longues (TBT réduit à ~0-50ms)
- **-347.6 Kio** de JavaScript inutilisé

---

### **2.  Logo LCP optimisé (Gain estimé: +1-2 points)**

**Fichier modifié** : `/templates/base.html.twig` (ligne 2841)

**Avant** :
```html
<img class="logo-infpf" alt="Logo formation" 
     src="{{ asset('img/CROPPED_LOGO__INFPF_2_150.png') }}" 
     width="150" height="161"/>
```

**Après** :
```html
<img class="logo-infpf" alt="Logo formation" 
     src="{{ asset('img/CROPPED_LOGO__INFPF_2_150.png') }}" 
     width="150" height="161" 
     fetchpriority="high" loading="eager"/>
```

**Bénéfices** :
- Le logo (élément LCP) est chargé avec la plus haute priorité
- **LCP réduit** de ~2.3s à ~1.5-1.8s (estimation)

---

### **3. 🔗 Préconnexions optimisées (Gain estimé: +0.5-1 point)**

**Fichier modifié** : `/templates/base.html.twig` (lignes 37-42)

**Avant** :
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://www.google.com">
<link rel="dns-prefetch" href="https://www.gstatic.com">
<link rel="dns-prefetch" href="https://unpkg.com">
<link rel="dns-prefetch" href="https://assets.calendly.com">
```

**Après** :
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://unpkg.com">
<link rel="dns-prefetch" href="https://assets.calendly.com">
<!-- Google reCAPTCHA chargé en lazy load uniquement à l'ouverture de la modal -->
```

**Changements** :
-  **Supprimé** : préconnexions Google/gstatic (reCAPTCHA lazy load)
- ⬆ **Upgrade** : unpkg.com de `dns-prefetch` à `preconnect` (AOS.js chargé immédiatement)

**Bénéfices** :
- **Connexion plus rapide** à unpkg.com (AOS.js)
- **Moins de connexions inutiles** (Google/gstatic ne sont plus nécessaires au chargement)

---

##  **SCORES ATTENDUS APRÈS OPTIMISATION**

| **Version** | **Avant** | **Après (Estimé)** | **Gain** |
|---|---|---|---|
| **Mobile** | 89/100 | **95-97/100**  | **+6-8 points** |
| **Desktop** | 98/100 | **99-100/100**  | **+1-2 points** |

### **Métriques attendues (Mobile)** :
- **TBT** : 240ms → **~0-50ms**  (-190ms)
- **LCP** : 2.3s → **~1.5-1.8s**  (-0.5-0.8s)
- **FCP** : 2.0s → **~1.5-1.7s**  (-0.3-0.5s)
- **Speed Index** : 4.6s → **~3.2-3.8s**  (-1.4-0.8s)

---

## 🧪 **TEST LIGHTHOUSE**

### **Mobile** :
```
https://pagespeed.web.dev/analysis?url=https://dev.infpf.fr/formation
```

### **Desktop** :
```
https://pagespeed.web.dev/analysis?url=https://dev.infpf.fr/formation&strategy=desktop
```

---

##  **OPTIMISATIONS FUTURES POSSIBLES**

### **1. Minification CSS inline (Gain: +1-2 points)**
- **Fichier** : `/templates/home/formation.html.twig`
- **CSS inline** : ~25 Kio → minifier pour économiser 11 Kio
- **Script PHP de minification** : À créer si besoin

### **2. Lazy load AOS.js (Gain: +0.5-1 point)**
- **Fichier** : `/templates/home/formation.html.twig` (ligne 2869)
- **Actuellement** : AOS.js chargé immédiatement (5.2 Kio)
- **Solution** : Charger AOS.js via Intersection Observer (au scroll)

### **3. Optimisation images footer (Gain: +0.5 point)**
- **Logos réseaux sociaux** : Facebook, YouTube, Instagram
- **Logos certifications** : Qualiopi, Datadock
- **Solution** : Conversion WebP + dimensions explicites (déjà fait pour la page d'accueil)

---

##  **OBJECTIFS ATTEINTS**

 **reCAPTCHA en lazy load** (690 Kio économisés au chargement)  
 **Logo LCP optimisé** (fetchpriority="high" + loading="eager")  
 **Préconnexions optimisées** (unpkg.com preconnect, Google/gstatic supprimés)  
 **Cache vidé** (Symfony prod)

---

##  **FICHIERS MODIFIÉS**

1. `/templates/components/modal.html.twig` - Lazy load reCAPTCHA
2. `/templates/base.html.twig` - Logo LCP + préconnexions optimisées

---

##  **PROCHAINE ÉTAPE**

**RE-TEST LIGHTHOUSE** sur la page `/formation` :
- URL Mobile : https://pagespeed.web.dev/analysis?url=https://dev.infpf.fr/formation
- URL Desktop : https://pagespeed.web.dev/analysis?url=https://dev.infpf.fr/formation&strategy=desktop

**Objectif** :
-  Mobile : **95-97/100** (au lieu de 89/100)
-  Desktop : **99-100/100** (au lieu de 98/100)

---

**Date** : 2025-11-04  
**Développeur** : Assistant IA  
**Environnement** : dev.infpf.fr  
**Branche Git** : `feature/performance-security-seo-optimization`

