# Changelog - Site INFPF

Toutes les modifications notables de ce projet sont documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Semantic Versioning](https://semver.org/lang/fr/).

---

## [Non publié]

### Ajouté
- Système de sauvegardes automatiques de la base de données
- Fichier `.env.example` pour la documentation
- Script de monitoring de santé du site
- Rate limiting sur les formulaires critiques
- Documentation SECURITY.md pour le signalement de vulnérabilités

### Modifié
- Configuration des logs de production (redirection vers fichier)
- Activation de HSTS pour sécurité HTTPS renforcée

---

## [3.0.0] - 2025-11-20

### Ajouté
- Responsive tablette : Menu burger et logo adaptatifs (768px-1199px)
- Optimisation des icônes et alignements sur tablette
- Media queries spécifiques pour format tablette

### Modifié
- Taille du logo adaptée par breakpoint (80px mobile, 100px tablette, 150px desktop)
- Alignement des sections sur page formation (show.html.twig)

---

## [2.0.0] - 2025-10-30 - Version Mobile Complète

### Ajouté
- Version mobile complète du site (55 fichiers modifiés)
- Menu mobile ultra-optimisé avec gestes tactiles
- Navigation tactile avec zones de touche 48px+
- Lazy loading des images pour performance mobile
- Breakpoints personnalisés pour tous les écrans

### Modifié
- Templates responsive pour tous les composants
- Optimisation des performances mobile (PageSpeed 97/100)
- Amélioration de l'accessibilité tactile

### Résultat
- +6,713 lignes ajoutées
- -2,056 lignes supprimées
- 55 fichiers modifiés

---

## [1.0.0] - 2025-09-30 - Refonte Desktop

### Ajouté
- Refonte complète du site desktop
- Système de blog avec publication programmée
- Mega-menu style Apple avec animations
- Intégration reCAPTCHA v3
- Intégration Stripe pour paiements
- Intégration Calendly pour rendez-vous
- Analytics personnalisé RGPD conforme
- Backend optimisé (Services, Repositories, Commands)

### Sécurité
- Headers HTTP de sécurité (XSS, CSP, X-Frame-Options)
- Rate Limiting configuré
- Authentification Symfony sécurisée

### Performance
- Cache HTTP agressif (1 an pour assets)
- Compression Gzip niveau 9
- WebP automatique
- CSS/JS minifiés

### Résultat
- +16,689 lignes ajoutées
- -3,003 lignes supprimées
- 41 fichiers modifiés

---

## [0.1.0] - 2025-04-01 - Initialisation

### Ajouté
- Configuration initiale Symfony 6.4
- Structure MVC de base
- Configuration Doctrine ORM
- EasyAdmin pour l'administration
- Configuration de base de la sécurité

---

## Types de changements

- `Ajouté` pour les nouvelles fonctionnalités
- `Modifié` pour les changements aux fonctionnalités existantes
- `Déprécié` pour les fonctionnalités qui seront bientôt supprimées
- `Supprimé` pour les fonctionnalités supprimées
- `Corrigé` pour les corrections de bugs
- `Sécurité` pour les vulnérabilités corrigées
