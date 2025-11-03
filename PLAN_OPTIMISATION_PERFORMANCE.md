# 🚀 Plan d'Optimisation Performance Page par Page

**Date** : 3 novembre 2025  
**Objectif** : Lighthouse Performance 90+ sur TOUTES les pages  
**Méthode** : Analyse et optimisation page par page

---

## 📋 Liste des Pages à Tester et Optimiser

### **Groupe 1 : Pages Principales** (Priorité 1)

| # | Type de Page | URL | Poids Estimé | Complexité | Priorité |
|---|--------------|-----|--------------|------------|----------|
| 1 | **Page d'accueil** | `https://dev.infpf.fr/` | Moyen | Moyenne | ⭐⭐⭐⭐⭐ |
| 2 | **Liste formations** | `https://dev.infpf.fr/formation` | **Lourd** (48 formations) | **Haute** | ⭐⭐⭐⭐⭐ |
| 3 | **Détail formation** | `https://dev.infpf.fr/formation/87` | Moyen | Moyenne | ⭐⭐⭐⭐ |
| 4 | **Liste formations filtrée** | `https://dev.infpf.fr/formation?thematique[]=7` | Lourd | Haute | ⭐⭐⭐⭐ |

### **Groupe 2 : Pages Statiques** (Priorité 2)

| # | Type de Page | URL | Poids Estimé | Complexité | Priorité |
|---|--------------|-----|--------------|------------|----------|
| 5 | **Financer ma formation** | `https://dev.infpf.fr/financer-ma-formation` | Léger | Faible | ⭐⭐⭐ |
| 6 | **Formations CPF** | `https://dev.infpf.fr/formations-eligibles-cpf` | Moyen | Moyenne | ⭐⭐⭐ |
| 7 | **Notre équipe** | `https://dev.infpf.fr/notre-equipe-pedagogique` | Moyen | Moyenne | ⭐⭐⭐ |
| 8 | **Contact** | `https://dev.infpf.fr/contactez-nous` | Léger | Faible | ⭐⭐ |

### **Groupe 3 : Pages Métiers** (Priorité 3)

| # | Type de Page | URL | Poids Estimé | Complexité | Priorité |
|---|--------------|-----|--------------|------------|----------|
| 9 | **Métier : Manager** | `https://dev.infpf.fr/metiers/manager` | Moyen | Moyenne | ⭐⭐ |
| 10 | **Métier : Trader** | `https://dev.infpf.fr/metiers/trader-finance` | Moyen | Moyenne | ⭐⭐ |

### **Groupe 4 : Blog** (Priorité 4)

| # | Type de Page | URL | Poids Estimé | Complexité | Priorité |
|---|--------------|-----|--------------|------------|----------|
| 11 | **Index blog** | `https://dev.infpf.fr/blog/` | Lourd | Haute | ⭐⭐⭐ |
| 12 | **Article blog** | `https://dev.infpf.fr/blog/[slug]` | Moyen | Moyenne | ⭐⭐ |

---

## 🔍 Analyse Préliminaire : Page Liste Formations

**URL testée** : https://dev.infpf.fr/formation

### Problèmes Identifiés (d'après le contenu)

1. **48 formations affichées** → DOM très lourd
2. **Chaque formation a une image** → 48 images à charger
3. **Filtres dynamiques** → JavaScript important
4. **Pas de pagination** → Tout chargé d'un coup
5. **Formulaire de demande de doc** → Modal avec tous les champs

### Optimisations à Appliquer

#### 1. **Pagination ou Lazy Loading** ⭐⭐⭐⭐⭐
- Afficher 12 formations par page au lieu de 48
- OU Lazy loading : charger 12, puis charger au scroll
- **Impact attendu** : -60% du DOM, -60% du temps de chargement

#### 2. **Images Optimisées** ⭐⭐⭐⭐
- Convertir les images en WebP
- Utiliser `srcset` pour responsive
- Lazy loading déjà appliqué (à vérifier)
- **Impact attendu** : -40% du poids des images

#### 3. **Defer JavaScript Filtres** ⭐⭐⭐
- Charger les scripts de filtres en defer
- Initialiser les filtres après le DOMContentLoaded
- **Impact attendu** : -20% du temps de blocage

#### 4. **Cache Fragment Twig** ⭐⭐⭐⭐
- Mettre en cache la liste des formations (TTL 1h)
- Mettre en cache les filtres (TTL 24h)
- **Impact attendu** : -50% du temps serveur

---

## 🧪 Protocole de Test

Pour chaque page, nous allons :

### **1. Mesurer l'État Actuel**
```bash
# Lighthouse CLI (plus précis que l'outil navigateur)
lighthouse https://dev.infpf.fr/formation \
  --only-categories=performance \
  --output=json \
  --output-path=./lighthouse-formation-before.json \
  --chrome-flags="--headless"
```

**Métriques à noter** :
- ✅ **Performance Score** (objectif : 90+)
- ✅ **LCP** (Largest Contentful Paint) (objectif : < 2.5s)
- ✅ **FID** (First Input Delay) (objectif : < 100ms)
- ✅ **CLS** (Cumulative Layout Shift) (objectif : < 0.1)
- ✅ **TBT** (Total Blocking Time) (objectif : < 200ms)
- ✅ **Speed Index** (objectif : < 3.4s)

### **2. Identifier les Problèmes Spécifiques**

Dans Lighthouse, regarder :
- 🔴 **Opportunities** : Gains de temps potentiels
- 🟡 **Diagnostics** : Problèmes à corriger
- 🟢 **Passed audits** : Ce qui fonctionne

### **3. Appliquer les Optimisations**

Selon les résultats, nous allons :
- Optimiser les images (WebP, srcset, compression)
- Réduire le JavaScript (code splitting, defer)
- Réduire le CSS (critical CSS inline)
- Ajouter la pagination
- Mettre en cache avec Twig

### **4. Re-tester et Comparer**
```bash
lighthouse https://dev.infpf.fr/formation \
  --only-categories=performance \
  --output=json \
  --output-path=./lighthouse-formation-after.json \
  --chrome-flags="--headless"
```

### **5. Documenter les Gains**

| Métrique | Avant | Après | Gain | Objectif Atteint |
|----------|-------|-------|------|------------------|
| Performance Score | ? | ? | +? | ✅/❌ |
| LCP | ?s | ?s | -?s | ✅/❌ |
| TBT | ?ms | ?ms | -?ms | ✅/❌ |
| CLS | ? | ? | ? | ✅/❌ |

---

## 📊 Optimisations Transversales (Toutes Pages)

Ces optimisations s'appliquent à **toutes** les pages :

### **✅ Déjà Appliqué**
- [x] Meta viewport
- [x] JavaScript defer (jQuery, popup.js)
- [x] Lazy loading images (logo, scroll-to-top)
- [x] Cache HTTP (.htaccess)
- [x] Compression Gzip/Brotli (.htaccess)
- [x] Preconnect (ajax.googleapis.com)

### **⏳ À Appliquer**

#### 1. **Critical CSS Inline** ⭐⭐⭐⭐
Extraire le CSS critique et le mettre inline dans `<head>` :
```twig
<style>
  /* CSS critique : header, above-the-fold content */
  header { ... }
  .hero { ... }
</style>
<link rel="preload" href="{{ asset('css/main.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
```

#### 2. **WebP Images** ⭐⭐⭐⭐⭐
Convertir toutes les images PNG/JPG en WebP :
```bash
# Conversion en masse
for img in public/images/*.{jpg,jpeg,png}; do
  cwebp "$img" -o "${img%.*}.webp"
done
```

Utiliser `<picture>` dans Twig :
```twig
<picture>
  <source srcset="{{ asset('images/logo.webp') }}" type="image/webp">
  <img src="{{ asset('images/logo.png') }}" alt="Logo" loading="lazy">
</picture>
```

#### 3. **Pagination Liste Formations** ⭐⭐⭐⭐⭐
Limiter à 12 formations par page dans `HomeController.php` :
```php
public function formation(Request $request, FormationRepository $formationRepository): Response
{
    $page = $request->query->getInt('page', 1);
    $limit = 12;
    $offset = ($page - 1) * $limit;
    
    $formations = $formationRepository->findBy(
        [], // critères
        ['id' => 'DESC'], // tri
        $limit, // limite
        $offset // offset
    );
    
    $total = $formationRepository->count([]);
    $totalPages = ceil($total / $limit);
    
    return $this->render('home/formation.html.twig', [
        'formations' => $formations,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        // ...
    ]);
}
```

#### 4. **Cache Twig HTTP** ⭐⭐⭐
Ajouter des headers de cache pour les pages statiques dans les contrôleurs :
```php
$response = $this->render('content/ecole/financer-ma-formation.html.twig', [...]);
$response->setSharedMaxAge(3600); // Cache 1h
$response->headers->addCacheControlDirective('must-revalidate');
return $response;
```

#### 5. **Font Display Swap** ⭐⭐⭐
Si vous utilisez Google Fonts, ajouter `&display=swap` :
```html
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
```

#### 6. **Preload Resources** ⭐⭐
Précharger les ressources critiques :
```twig
<link rel="preload" href="{{ asset('fonts/roboto.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="{{ asset('images/hero.webp') }}" as="image">
```

---

## 🎯 Plan d'Action par Priorité

### **Phase 1 : Quick Wins (1-2h)** ⚡
1. Ajouter la pagination sur `/formation` (12 par page)
2. Optimiser les images existantes (compression + WebP)
3. Ajouter `font-display: swap` aux fonts
4. Preload des ressources critiques

**Gain attendu** : Performance Score +10-15 points

---

### **Phase 2 : Optimisations Moyennes (2-4h)** 🔧
1. Extraire et inline le Critical CSS
2. Mettre en cache les réponses HTTP (contrôleurs)
3. Lazy loading sur TOUTES les images (vérification)
4. Code splitting JavaScript (si possible)

**Gain attendu** : Performance Score +5-10 points

---

### **Phase 3 : Optimisations Avancées (4-8h)** 🚀
1. Service Worker pour cache offline
2. HTTP/2 Server Push (si Hostinger supporte)
3. Minification avancée CSS/JS (Webpack)
4. Optimisation base de données (index)

**Gain attendu** : Performance Score +5 points

---

## 📝 Checklist par Page

### Page : `/formation` (Liste formations)
- [ ] Test Lighthouse initial
- [ ] Ajouter pagination (12/page)
- [ ] Optimiser images formations (WebP)
- [ ] Lazy loading images
- [ ] Cache fragment Twig
- [ ] Defer JavaScript filtres
- [ ] Test Lighthouse final
- [ ] Documenter gains

### Page : `/` (Accueil)
- [ ] Test Lighthouse initial
- [ ] Critical CSS inline
- [ ] Optimiser images hero
- [ ] Preload ressources critiques
- [ ] Test Lighthouse final
- [ ] Documenter gains

### Page : `/formation/{id}` (Détail)
- [ ] Test Lighthouse initial
- [ ] Optimiser images formation
- [ ] Cache HTTP (1h)
- [ ] Lazy loading contenu long
- [ ] Test Lighthouse final
- [ ] Documenter gains

*(Répéter pour chaque type de page)*

---

## 🧪 Commandes de Test

### Tester Lighthouse en CLI (Plus Précis)
```bash
# Installation (si nécessaire)
npm install -g lighthouse

# Test page formation
lighthouse https://dev.infpf.fr/formation \
  --only-categories=performance \
  --output=html \
  --output-path=./reports/formation-$(date +%Y%m%d-%H%M%S).html \
  --chrome-flags="--headless --no-sandbox"

# Test toutes les pages principales
for url in "/" "/formation" "/formation/87" "/financer-ma-formation"; do
  lighthouse "https://dev.infpf.fr$url" \
    --only-categories=performance \
    --output=json \
    --output-path="./reports/$(echo $url | tr '/' '-').json"
done
```

### Tester WebPageTest (Très Complet)
1. Aller sur https://www.webpagetest.org
2. Entrer l'URL : `https://dev.infpf.fr/formation`
3. Test Location : **Paris, France** (le plus proche)
4. Browser : **Chrome**
5. Lancer le test
6. Analyser les Core Web Vitals

### Tester GTmetrix
1. Aller sur https://gtmetrix.com
2. Entrer l'URL : `https://dev.infpf.fr/formation`
3. Test Server : **London, UK** (Europe)
4. Lancer le test
5. Télécharger le rapport PDF

---

## 📊 Tableau de Suivi

| Page | Score Initial | Score Cible | Score Final | Date Test | Statut |
|------|---------------|-------------|-------------|-----------|--------|
| `/` | ? | 90+ | ? | - | ⏳ À tester |
| `/formation` | ? | 90+ | ? | - | ⏳ À tester |
| `/formation/87` | ? | 90+ | ? | - | ⏳ À tester |
| `/financer-ma-formation` | ? | 95+ | ? | - | ⏳ À tester |
| `/formations-eligibles-cpf` | ? | 95+ | ? | - | ⏳ À tester |
| `/notre-equipe-pedagogique` | ? | 95+ | ? | - | ⏳ À tester |
| `/blog/` | ? | 90+ | ? | - | ⏳ À tester |

---

## 🎯 Objectifs Finaux

| Métrique | Objectif | Réaliste |
|----------|----------|----------|
| **Performance Score** | **90-95** | ✅ Très réaliste |
| **LCP** | < 2.5s | ✅ Réaliste avec pagination + WebP |
| **FID** | < 100ms | ✅ Réaliste avec defer JS |
| **CLS** | < 0.1 | ✅ Réaliste (pas de pubs) |
| **TBT** | < 200ms | ✅ Réaliste avec optimisation JS |
| **Speed Index** | < 3.4s | ✅ Réaliste |

---

**Prochaine Étape** : Tester les 12 pages listées avec Lighthouse et identifier les quick wins.

**Date de dernière mise à jour** : 3 novembre 2025, 15:15 UTC

