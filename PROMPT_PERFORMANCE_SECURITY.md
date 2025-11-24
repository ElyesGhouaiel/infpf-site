#  PROMPT - Optimisation Performance, Sécurité & SEO - Site INFPF

##  CONTEXTE DU PROJET

### Informations Générales
- **Projet** : Site web INFPF (Institut National de la Formation Professionnelle)
- **Framework** : Symfony 6.4
- **PHP** : 8.1+
- **Serveur** : Hostinger (Linux)
- **URL Production** : https://infpf.fr/
- **URL Développement** : https://dev.infpf.fr/
- **Repository GitHub** : https://github.com/ElyesGhouaiel/infpf-site
- **Branche de travail actuelle** : `feature/performance-security-seo-optimization`

### Structure du Projet
```
/home/u665392393/domains/infpf.fr/
├── public_html/          # Production (branche main)
│   ├── public/           # Document root web
│   ├── src/              # Code source PHP
│   ├── templates/        # Templates Twig
│   ├── config/           # Configuration Symfony
│   ├── assets/           # Assets frontend
│   └── var/              # Cache et logs
└── dev/                  # Développement (branche dev)
    └── [même structure]
```

### Technologies & Dépendances Clés
- **Backend** :
  - Symfony 6.4 (Framework PHP)
  - Doctrine ORM 2.17 (Base de données)
  - EasyAdmin Bundle 4.10 (Interface admin)
  - Symfony Mailer (Email)
  - Symfony Security Bundle (Authentification)
  - Stripe PHP SDK 14.6 (Paiements)
  
- **Frontend** :
  - CSS3 (Variables, Flexbox, Grid, Media Queries)
  - JavaScript ES6+ (Modules, Async/Await, DOM)
  - AOS 2.3.1 (Animations)
  - jQuery 3.5.1
  - Calendly (Intégration widget)
  
- **Sécurité** :
  - reCAPTCHA v3 (Protection formulaires)
  - CSRF Protection (Symfony)
  - Secure File Uploads
  
- **Analytics** :
  - Système custom RGPD-compliant
  - Cookie consent banner
  - Dashboard analytics

### Workflow Git
- **Branches** :
  - `main` : Production (infpf.fr)
  - `dev` : Développement (dev.infpf.fr)
  - `feature/*` : Branches de fonctionnalités
  
- **Environnements** :
  - Production : `/public_html/` (branche `main`)
  - Dev : `/dev/` (branche `dev` ou `feature`)

### État Actuel du Site
-  Refonte desktop complète (Avril-Septembre 2025)
-  Version mobile responsive (Octobre 2025)
-  Blog avec CRUD complet
-  Pages formations modernisées
-  8 pages école refondues
-  Système analytics RGPD
-  Intégration Calendly
-  Workflow Git professionnel

---

##  MISSION : OPTIMISATION GLOBALE

### Objectifs Prioritaires

#### 1.  PERFORMANCE (Priorité HAUTE)
**Objectif** : Rendre le site ultra-rapide sur desktop ET mobile

**Métriques cibles** :
- Lighthouse Performance Score : **≥ 90/100**
- First Contentful Paint (FCP) : **< 1.8s**
- Largest Contentful Paint (LCP) : **< 2.5s**
- Time to Interactive (TTI) : **< 3.8s**
- Total Blocking Time (TBT) : **< 300ms**
- Cumulative Layout Shift (CLS) : **< 0.1**

**Actions à mener** :
- [ ] Optimiser les images (WebP, lazy loading, responsive images)
- [ ] Minifier et combiner CSS/JS
- [ ] Mettre en place le cache HTTP (headers, ETags)
- [ ] Optimiser le cache Symfony
- [ ] Implémenter le préchargement des ressources critiques
- [ ] Réduire le JavaScript non utilisé
- [ ] Différer le chargement des scripts non critiques
- [ ] Optimiser les fonts (preload, font-display: swap)
- [ ] Mettre en place un CDN (si possible)
- [ ] Optimiser les requêtes database (indexes, eager loading)
- [ ] Réduire le TTFB (Time To First Byte)

#### 2. ♿ ACCESSIBILITÉ (Priorité HAUTE)
**Objectif** : Rendre le site accessible à tous (WCAG 2.1 niveau AA minimum)

**Métriques cibles** :
- Lighthouse Accessibility Score : **≥ 95/100**
- Conformité WCAG 2.1 AA : **100%**

**Actions à mener** :
- [ ] Ajouter les attributs ARIA manquants
- [ ] Assurer le contraste des couleurs (≥ 4.5:1)
- [ ] Navigation au clavier complète
- [ ] Labels pour tous les formulaires
- [ ] Attributs alt pour toutes les images
- [ ] Hierarchie des titres (h1, h2, h3...) correcte
- [ ] Support lecteurs d'écran
- [ ] Focus visible sur tous les éléments interactifs
- [ ] Texte redimensionnable jusqu'à 200%
- [ ] Pas de timeout sur les formulaires

#### 3. 🔒 SÉCURITÉ (Priorité CRITIQUE)
**Objectif** : Sécurité maximale (niveau production enterprise)

**Métriques cibles** :
- Mozilla Observatory : **A+**
- SecurityHeaders.com : **A+**
- Zéro vulnérabilité critique ou haute

**Actions à mener** :
- [ ] **Headers de sécurité** :
  - Content-Security-Policy (CSP)
  - X-Frame-Options: DENY
  - X-Content-Type-Options: nosniff
  - Strict-Transport-Security (HSTS)
  - Referrer-Policy: strict-origin-when-cross-origin
  - Permissions-Policy
  
- [ ] **Protection XSS** :
  - Échapper toutes les sorties Twig
  - Sanitiser les inputs utilisateur
  - CSP strict
  
- [ ] **Protection CSRF** :
  - Tokens CSRF sur tous les formulaires
  - Validation côté serveur
  
- [ ] **Protection SQL Injection** :
  - Utiliser Doctrine ORM (paramètres bindés)
  - Valider tous les inputs
  
- [ ] **Authentification & Sessions** :
  - Sessions sécurisées (httponly, secure, samesite)
  - Rate limiting sur login
  - Password hashing bcrypt/argon2
  - 2FA si applicable
  
- [ ] **File Uploads** :
  - Validation type MIME
  - Limite de taille
  - Renommage des fichiers
  - Stockage hors webroot si possible
  
- [ ] **Dependencies** :
  - Audit composer (composer audit)
  - Mise à jour sécurité régulières
  - Pas de dépendances abandonnées
  
- [ ] **Configuration** :
  - .env non accessible publiquement
  - Debug mode OFF en production
  - Logs d'erreur sécurisés
  - Permissions fichiers correctes

#### 4.  SEO (Priorité HAUTE)
**Objectif** : Référencement optimal sur Google

**Métriques cibles** :
- Lighthouse SEO Score : **100/100**
- Core Web Vitals : **Tous verts**
- Mobile-Friendly Test : **100%**

**Actions à mener** :
- [ ] Balises meta optimisées (title, description)
- [ ] Open Graph et Twitter Cards
- [ ] Sitemap.xml dynamique
- [ ] Robots.txt optimisé
- [ ] URLs SEO-friendly
- [ ] Schema.org markup (JSON-LD)
- [ ] Canonical URLs
- [ ] Images optimisées (alt, title, taille)
- [ ] Contenu sémantique (header, nav, main, footer, article)
- [ ] Fil d'Ariane (breadcrumbs)
- [ ] 404 page personnalisée
- [ ] Redirections 301 pour ancien contenu
- [ ] Core Web Vitals optimisés

#### 5.  BONNES PRATIQUES (Priorité MOYENNE)
**Objectif** : Code propre et maintenable

**Métriques cibles** :
- Lighthouse Best Practices : **100/100**
- Zéro erreur console
- Zéro warning critique

**Actions à mener** :
- [ ] HTTPS partout (mixed content = 0)
- [ ] Pas d'erreurs console JS
- [ ] Gérer les erreurs 404 proprement
- [ ] Logs d'erreur appropriés
- [ ] Pas de bibliothèques obsolètes
- [ ] Images avec dimensions explicites (éviter CLS)
- [ ] Compression Gzip/Brotli activée
- [ ] Browser caching optimal

#### 6. ⚙ CI/CD - GITHUB ACTIONS (Priorité HAUTE)
**Objectif** : Pipeline automatisé pour qualité et sécurité

**Actions à mener** :
- [ ] **Pipeline CI** :
  - Tests automatisés (PHPUnit si applicable)
  - Lint PHP (PHP-CS-Fixer, PHPStan)
  - Audit sécurité (composer audit)
  - Tests Lighthouse CI
  - Validation accessibility (pa11y)
  
- [ ] **Pipeline CD** :
  - Déploiement automatique sur dev
  - Review apps pour PR
  - Déploiement manuel vers production (avec approbation)
  
- [ ] **Monitoring** :
  - Alertes sur échecs de build
  - Reports Lighthouse automatiques
  - Notifications Slack/Email

---

## 📁 FICHIERS IMPORTANTS À ANALYSER

### Configuration Symfony
```
config/packages/framework.yaml       # Cache, sessions
config/packages/security.yaml        # Sécurité
config/packages/twig.yaml            # Templates
config/packages/doctrine.yaml        # Database
config/routes.yaml                   # Routes
.env                                 # Variables d'environnement
```

### Templates Critiques
```
templates/base.html.twig             # Layout principal (header, meta, CSS)
templates/home/home.html.twig        # Homepage
templates/content/formation/show.html.twig  # Pages formations
templates/content/blog/show.html.twig      # Articles blog
templates/content/ecole/*.html.twig        # Pages école
```

### Controllers
```
src/Controller/HomeController.php
src/Controller/BlogController.php
src/Controller/FormationController.php
src/Controller/ContactController.php
```

### Assets
```
assets/bootstrap.js                  # Point d'entrée JS
public/                              # Assets publics
```

### Configuration Serveur
```
public/.htaccess                     # Configuration Apache
```

---

## 🛠 OUTILS À UTILISER

### Analyse & Audit
- **Google Lighthouse** : Performance, Accessibility, SEO, Best Practices
- **WebPageTest** : Performance détaillée
- **GTmetrix** : Performance + recommandations
- **Google PageSpeed Insights** : Core Web Vitals
- **WAVE** : Accessibilité
- **axe DevTools** : Accessibilité
- **SecurityHeaders.com** : Headers de sécurité
- **Mozilla Observatory** : Sécurité globale
- **Snyk** : Vulnérabilités dépendances

### Optimisation
- **ImageOptim / TinyPNG** : Compression images
- **Squoosh** : Conversion WebP
- **PurgeCSS** : Suppression CSS inutilisé
- **Terser** : Minification JS
- **CSSNano** : Minification CSS

### CI/CD
- **GitHub Actions** : Pipeline CI/CD
- **Lighthouse CI** : Tests performance automatisés
- **pa11y** : Tests accessibilité automatisés
- **PHPStan** : Analyse statique PHP

---

##  DIAGNOSTIC INITIAL À RÉALISER

### 1. Audit Lighthouse
```bash
# Tester la page d'accueil
npx lighthouse https://dev.infpf.fr/ --output html --output-path ./reports/lighthouse-home.html

# Tester une page formation
npx lighthouse https://dev.infpf.fr/formation/[slug] --output html --output-path ./reports/lighthouse-formation.html
```

### 2. Audit Sécurité
```bash
# Audit des dépendances Composer
composer audit

# Analyser les headers HTTP
curl -I https://dev.infpf.fr/
```

### 3. Analyse des Assets
```bash
# Taille des images
du -sh public/img/*
du -sh public/uploads/images/*

# Taille du CSS/JS
du -sh public/build/* 2>/dev/null || echo "Pas de build webpack"
```

### 4. Performance Database
```bash
# Activer le profiler Symfony
php bin/console debug:container --env=dev | grep profiler
```

---

##  PLAN D'ACTION SUGGÉRÉ

### Phase 1 : DIAGNOSTIC (Jour 1)
1. Audit Lighthouse complet (toutes les pages principales)
2. Audit sécurité (headers, composer audit)
3. Analyse des assets (taille, format)
4. Review du code (templates, controllers)
5. Documentation des problèmes trouvés

### Phase 2 : SÉCURITÉ (Jour 1-2)
1. Implémenter les headers de sécurité
2. Audit des formulaires (CSRF, validation)
3. Review des uploads de fichiers
4. Audit des sessions
5. Tests de sécurité

### Phase 3 : PERFORMANCE (Jour 2-3)
1. Optimisation des images
2. Minification CSS/JS
3. Configuration du cache
4. Optimisation des requêtes DB
5. Lazy loading
6. Preload des ressources critiques

### Phase 4 : ACCESSIBILITÉ (Jour 3-4)
1. Audit ARIA
2. Contraste des couleurs
3. Navigation clavier
4. Labels et alt texts
5. Tests avec lecteurs d'écran

### Phase 5 : SEO (Jour 4)
1. Meta tags
2. Sitemap.xml
3. Schema.org
4. URLs
5. Core Web Vitals

### Phase 6 : CI/CD (Jour 5)
1. Configuration GitHub Actions
2. Lint et tests automatisés
3. Lighthouse CI
4. Documentation du pipeline

### Phase 7 : TESTS & VALIDATION (Jour 6)
1. Tests complets sur dev.infpf.fr
2. Validation Lighthouse (tous scores ≥ 90)
3. Validation sécurité (A+ partout)
4. Tests manuels
5. Documentation

---

##  CRITÈRES DE SUCCÈS

### Scores Lighthouse Minimum
-  Performance : **≥ 90/100**
-  Accessibility : **≥ 95/100**
-  Best Practices : **100/100**
-  SEO : **100/100**

### Sécurité
-  Mozilla Observatory : **A+**
-  SecurityHeaders.com : **A+**
-  Composer audit : **0 vulnérabilités**

### Performance Web Vitals
-  LCP : **< 2.5s**
-  FID : **< 100ms**
-  CLS : **< 0.1**

### CI/CD
-  Pipeline GitHub Actions fonctionnel
-  Tests automatisés qui passent
-  Déploiement automatisé vers dev

---

##  CONTRAINTES IMPORTANTES

### Environnement
- Serveur Hostinger (pas de root access)
- Pas d'accès à certaines configs serveur
- Optimisations limitées au niveau application

### Compatibilité
- Maintenir la compatibilité avec l'existant
- Ne pas casser les fonctionnalités actuelles
- Tester sur tous les navigateurs principaux

### Performance
- Privilégier les optimisations sans coût (images, cache)
- CDN gratuit si possible (Cloudflare)
- Pas de refactoring majeur du code

---

##  DOCUMENTATION À CRÉER

À la fin de la mission, créer :

1. **PERFORMANCE_REPORT.md** :
   - Scores avant/après
   - Optimisations appliquées
   - Gains mesurés
   
2. **SECURITY_REPORT.md** :
   - Vulnérabilités trouvées et corrigées
   - Headers configurés
   - Recommandations futures
   
3. **CI_CD_SETUP.md** :
   - Configuration GitHub Actions
   - Comment l'utiliser
   - Maintenance
   
4. **OPTIMIZATION_GUIDE.md** :
   - Guide des bonnes pratiques
   - Checklist pour futures features
   - Monitoring recommandé

---

## 🔗 RESSOURCES UTILES

### Documentation
- Symfony Performance: https://symfony.com/doc/current/performance.html
- Symfony Security: https://symfony.com/doc/current/security.html
- Web.dev (Google): https://web.dev/
- MDN Web Docs: https://developer.mozilla.org/

### Outils en ligne
- Lighthouse: https://pagespeed.web.dev/
- SecurityHeaders: https://securityheaders.com/
- Observatory: https://observatory.mozilla.org/
- WAVE: https://wave.webaim.org/

---

## 💬 PROMPT À COPIER-COLLER POUR LA PROCHAINE SESSION

```
Je travaille sur l'optimisation complète (Performance, Sécurité, SEO, Accessibilité) du site INFPF.

**Contexte** :
- Projet Symfony 6.4 hébergé sur Hostinger
- Repository : https://github.com/ElyesGhouaiel/infpf-site
- Branche de travail : feature/performance-security-seo-optimization
- Environnement de dev : https://dev.infpf.fr/
- Environnement de prod : https://infpf.fr/

**Mission** :
1.  PERFORMANCE : Scores Lighthouse ≥ 90, Core Web Vitals optimaux
2. ♿ ACCESSIBILITÉ : WCAG 2.1 AA, score ≥ 95
3. 🔒 SÉCURITÉ : Headers sécurisés (A+), audit composer, protection XSS/CSRF/SQL Injection
4.  SEO : Score 100, sitemap, schema.org, meta tags optimisés
5. ⚙ CI/CD : Pipeline GitHub Actions avec tests automatisés

**Fichier de référence** :
Lire le fichier `/home/u665392393/domains/infpf.fr/public_html/PROMPT_PERFORMANCE_SECURITY.md` pour le contexte complet.

**Première action demandée** :
Réaliser un audit complet (Lighthouse + Sécurité) et me présenter un plan d'action priorisé avec les quick wins en premier.

Commence par analyser l'état actuel du projet et propose-moi les 5 premières optimisations à impact maximal.
```

---

*Prompt créé le 30 octobre 2025 - Elyes Ghouaiel*
*Branche : feature/performance-security-seo-optimization*
*Objectif : Site ultra-rapide, sécurisé et optimisé pour le SEO*

