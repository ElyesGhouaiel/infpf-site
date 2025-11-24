#  DÉMARRER UNE NOUVELLE SESSION - OPTIMISATION PERFORMANCE & SÉCURITÉ

##  TOUT EST PRÊT !

La branche `feature/performance-security-seo-optimization` a été créée et est maintenant active dans votre environnement de développement.

---

## 📍 ÉTAT ACTUEL

### Branches
-  **Branche créée** : `feature/performance-security-seo-optimization`
-  **Poussée sur GitHub** : https://github.com/ElyesGhouaiel/infpf-site
-  **Active dans /dev/** : Prête pour le développement

### Environnements
```
📁 /home/u665392393/domains/infpf.fr/public_html/
   └── Branche : feature/performance-security-seo-optimization

📁 /home/u665392393/domains/infpf.fr/dev/
   └── Branche : feature/performance-security-seo-optimization
   └── URL : https://dev.infpf.fr/
```

### Fichiers créés
-  **PROMPT_PERFORMANCE_SECURITY.md** : Prompt complet (TRÈS DÉTAILLÉ - 600+ lignes)
-  **BRANCH_README.md** : Aperçu et plan d'action
-  **START_NEW_SESSION.md** : Ce fichier (guide de démarrage)

---

##  PROMPT À COPIER-COLLER POUR NOUVELLE SESSION

### Version Courte (Recommandée)

Copiez-collez ceci dans votre prochaine conversation :

```
Je travaille sur l'optimisation complète du site INFPF (Performance, Sécurité, SEO, Accessibilité).

CONTEXTE :
- Projet Symfony 6.4 hébergé sur Hostinger
- Repository : https://github.com/ElyesGhouaiel/infpf-site
- Branche : feature/performance-security-seo-optimization
- Dev : https://dev.infpf.fr/
- Prod : https://infpf.fr/

MISSION :
1.  PERFORMANCE : Lighthouse ≥ 90, Core Web Vitals optimaux
2. ♿ ACCESSIBILITÉ : WCAG 2.1 AA, score ≥ 95
3. 🔒 SÉCURITÉ : Headers A+, audit composer, protections XSS/CSRF/SQL
4.  SEO : Score 100, sitemap, schema.org
5. ⚙ CI/CD : Pipeline GitHub Actions

PREMIÈRE ACTION :
Lis le fichier /home/u665392393/domains/infpf.fr/public_html/PROMPT_PERFORMANCE_SECURITY.md pour avoir tout le contexte détaillé, puis réalise un audit complet et propose-moi les 5 premières optimisations à impact maximal (quick wins en priorité).

Je veux que tu sois un expert en performance web et sécurité ! Commence par analyser l'état actuel du site.
```

### Version Longue (Si besoin de plus de détails dès le début)

```
Je travaille sur l'optimisation complète (Performance, Sécurité, SEO, Accessibilité) du site INFPF.

**Contexte détaillé** :
- Framework : Symfony 6.4, PHP 8.1+
- Serveur : Hostinger (Linux)
- Repository : https://github.com/ElyesGhouaiel/infpf-site
- Branche de travail : feature/performance-security-seo-optimization
- Environnement dev : https://dev.infpf.fr/ (branche feature active)
- Environnement prod : https://infpf.fr/ (branche main)

**Technologies** :
- Backend : Symfony 6.4, Doctrine ORM 2.17, EasyAdmin 4.10
- Frontend : CSS3 (Variables, Flexbox, Grid), JavaScript ES6+, AOS 2.3.1, jQuery 3.5.1
- Sécurité : reCAPTCHA v3, Symfony Security Bundle, Stripe PHP SDK 14.6
- Analytics : Système custom RGPD-compliant

**État actuel** :
-  Refonte desktop complète (Avril-Sept 2025)
-  Version mobile responsive (Octobre 2025)
-  Workflow Git professionnel (main/dev/feature)

**Mission** :
1.  **PERFORMANCE** :
   - Lighthouse Performance ≥ 90/100
   - LCP < 2.5s, FID < 100ms, CLS < 0.1
   - Images WebP + lazy loading
   - Minification CSS/JS
   - Cache HTTP optimal

2. ♿ **ACCESSIBILITÉ** :
   - Lighthouse Accessibility ≥ 95/100
   - WCAG 2.1 AA (100%)
   - ARIA attributes complets
   - Navigation clavier
   - Contraste ≥ 4.5:1

3. 🔒 **SÉCURITÉ** :
   - Mozilla Observatory A+
   - SecurityHeaders.com A+
   - CSP, HSTS, X-Frame-Options
   - Protection XSS/CSRF/SQL Injection
   - Composer audit : 0 vulnérabilités

4.  **SEO** :
   - Lighthouse SEO 100/100
   - Sitemap.xml + robots.txt
   - Schema.org (JSON-LD)
   - Meta tags optimisés
   - Core Web Vitals verts

5. ⚙ **CI/CD** :
   - GitHub Actions workflow
   - Tests automatisés (Lint, Audit)
   - Lighthouse CI
   - Déploiement auto vers dev

**Fichiers importants** :
- templates/base.html.twig (layout principal)
- templates/home/home.html.twig (homepage)
- config/packages/security.yaml
- public/.htaccess
- composer.json

**Première action demandée** :
1. Lire /home/u665392393/domains/infpf.fr/public_html/PROMPT_PERFORMANCE_SECURITY.md (contexte complet)
2. Réaliser un audit Lighthouse de https://dev.infpf.fr/
3. Analyser les headers de sécurité (curl -I ou SecurityHeaders.com)
4. Composer audit
5. Me présenter un rapport avec les 10 problèmes les plus critiques et un plan d'action priorisé

Je veux que tu sois un expert en performance web, sécurité et accessibilité. Commence l'audit !
```

---

## 🛠 COMMANDES UTILES

### Vérifier l'état actuel
```bash
# Voir la branche active
cd /home/u665392393/domains/infpf.fr/dev
git branch --show-current

# Voir les fichiers créés
ls -lah *.md

# Voir le statut Git
git status
```

### Travailler sur la branche
```bash
# Se positionner dans /dev/ (environnement de développement)
cd /home/u665392393/domains/infpf.fr/dev

# S'assurer d'être sur la bonne branche
git checkout feature/performance-security-seo-optimization

# Récupérer les dernières modifications
git pull origin feature/performance-security-seo-optimization

# Faire vos modifications...
# ... éditer les fichiers ...

# Commiter
git add .
git commit -m "perf: description de l'optimisation"

# Pousser sur GitHub
git push origin feature/performance-security-seo-optimization
```

### Tester vos modifications
```bash
# Nettoyer le cache Symfony
cd /home/u665392393/domains/infpf.fr/dev
php bin/console cache:clear

# Visiter le site de dev
# → https://dev.infpf.fr/
```

---

##  AUDITS À RÉALISER EN PREMIER

### 1. Lighthouse (Performance, Accessibility, SEO, Best Practices)
```bash
# Via navigateur (Chrome DevTools)
1. Ouvrir https://dev.infpf.fr/ dans Chrome
2. F12 → Onglet "Lighthouse"
3. Cocher toutes les catégories
4. "Analyze page load"

# Via CLI (si Node.js installé)
npx lighthouse https://dev.infpf.fr/ --output html --output-path ./reports/lighthouse-initial.html
```

### 2. Headers de Sécurité
```bash
# Via curl
curl -I https://dev.infpf.fr/

# Ou via outil en ligne
# → https://securityheaders.com/?q=https://dev.infpf.fr/
# → https://observatory.mozilla.org/analyze/dev.infpf.fr
```

### 3. Audit Composer (Vulnérabilités)
```bash
cd /home/u665392393/domains/infpf.fr/dev
composer audit
```

### 4. Analyse des Assets
```bash
cd /home/u665392393/domains/infpf.fr/dev

# Taille des images
du -sh public/img/*
du -sh public/uploads/images/*

# Liste des images > 500KB
find public/img -type f -size +500k -exec ls -lh {} \;
find public/uploads/images -type f -size +500k -exec ls -lh {} \;
```

---

## 📚 DOCUMENTATION DISPONIBLE

| Fichier | Contenu | Taille |
|---------|---------|--------|
| **PROMPT_PERFORMANCE_SECURITY.md** | 📖 Prompt COMPLET avec tout le contexte | ~600 lignes |
| **BRANCH_README.md** |  Aperçu de la branche et plan d'action | ~150 lignes |
| **START_NEW_SESSION.md** |  Ce fichier (guide de démarrage) | ~200 lignes |
| **FAQ_WORKFLOW.md** |  FAQ sur le workflow Git | ~270 lignes |
| **GIT_WORKFLOW.md** | 📚 Guide complet du workflow Git | ~280 lignes |
| **QUICK_START.md** |  Guide rapide du workflow | ~80 lignes |

**Recommandation** : Commencez par lire `PROMPT_PERFORMANCE_SECURITY.md` - c'est le document le plus important !

---

##  OBJECTIFS CHIFFRÉS

### Scores Lighthouse
| Catégorie | Cible | Actuel | À mesurer |
|-----------|-------|--------|-----------|
| Performance | ≥ 90 |  | 1er audit |
| Accessibility | ≥ 95 |  | 1er audit |
| Best Practices | 100 |  | 1er audit |
| SEO | 100 |  | 1er audit |

### Core Web Vitals
| Métrique | Cible | Actuel | À mesurer |
|----------|-------|--------|-----------|
| LCP (Largest Contentful Paint) | < 2.5s |  | PageSpeed Insights |
| FID (First Input Delay) | < 100ms |  | PageSpeed Insights |
| CLS (Cumulative Layout Shift) | < 0.1 |  | PageSpeed Insights |

### Sécurité
| Outil | Cible | Actuel | À mesurer |
|-------|-------|--------|-----------|
| Mozilla Observatory | A+ |  | observatory.mozilla.org |
| SecurityHeaders.com | A+ |  | securityheaders.com |
| Composer Audit | 0 vulnérabilités |  | composer audit |

---

##  QUICK WINS (Gains rapides)

Voici les optimisations qui devraient être faites en premier (impact élevé, effort faible) :

### Performance
1.  Activer la compression Gzip/Brotli (.htaccess)
2.  Ajouter les headers de cache (.htaccess)
3.  Optimiser les images (compression + WebP)
4.  Lazy loading des images
5.  Defer JavaScript non critique

### Sécurité
1.  Ajouter Content-Security-Policy
2.  Ajouter X-Frame-Options: DENY
3.  Ajouter X-Content-Type-Options: nosniff
4.  Ajouter Strict-Transport-Security (HSTS)
5.  Composer audit + mise à jour sécurité

### SEO
1.  Vérifier meta description sur toutes les pages
2.  Vérifier balises alt sur toutes les images
3.  Générer sitemap.xml
4.  Optimiser robots.txt
5.  Ajouter Schema.org (Organisation, WebSite)

### Accessibilité
1.  Contraste des couleurs (≥ 4.5:1)
2.  Attributs alt manquants
3.  Labels sur les formulaires
4.  ARIA attributes de base
5.  Navigation au clavier testée

---

## 📞 SUPPORT

Si vous avez des questions ou des blocages :
- 📖 Lisez `PROMPT_PERFORMANCE_SECURITY.md` (réponses détaillées)
- 📖 Lisez `FAQ_WORKFLOW.md` (questions Git)
- 📖 Lisez `GIT_WORKFLOW.md` (workflow complet)

---

##  CHECKLIST AVANT DE COMMENCER

- [x] Branche créée : `feature/performance-security-seo-optimization`
- [x] Branche poussée sur GitHub
- [x] Environnement /dev/ configuré sur la branche
- [x] Prompt détaillé créé (PROMPT_PERFORMANCE_SECURITY.md)
- [x] Documentation créée (BRANCH_README.md)
- [x] Guide de démarrage créé (START_NEW_SESSION.md)

** Vous êtes prêt à démarrer !**

---

## 🎬 PROCHAINES ÉTAPES

1. **Copiez le prompt** (version courte ou longue ci-dessus)
2. **Démarrez une nouvelle conversation** avec votre assistant AI
3. **Collez le prompt** pour donner tout le contexte
4. **L'assistant vous guidera** dans l'audit et les optimisations

---

*Guide créé le 30 octobre 2025 - Elyes Ghouaiel*
*Branche : feature/performance-security-seo-optimization*
*Objectif : Site ultra-rapide, sécurisé et optimisé*

**BON COURAGE ! **

