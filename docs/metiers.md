# Documentation - Fonctionnalité Métiers

## 📋 Vue d'ensemble

La fonctionnalité **Métiers** permet de regrouper les formations par thématiques professionnelles, offrant une navigation plus intuitive pour les utilisateurs souhaitant se former dans un domaine spécifique.

## 🎯 Fonctionnalités

### ✅ Ce qui a été implémenté

- **Page Hub Métiers** (`/metiers`) : Liste des thématiques métiers avec cartes responsive
- **Pages Métiers détaillées** (`/metiers/{slug}`) : Formations groupées par thématique
- **Navigation mise à jour** : Nouveau menu "Métiers" dans la barre de navigation
- **SEO optimisé** : Balises meta, JSON-LD, hreflang, sitemap ready
- **Toggle FR/EN** : Bascule de langue en haut à droite des pages
- **Responsive Design** : Interface mobile-first optimisée
- **Feature Flag** : Activation/désactivation via configuration

### 🔧 Architecture technique

```
src/
├── Controller/MetierController.php     # Contrôleur principal
├── Service/MetierService.php          # Logique métier et filtrage
config/
├── metiers.yaml                       # Configuration des thématiques
templates/metiers/
├── index.html.twig                    # Page hub des métiers
└── show.html.twig                     # Page détail d'un métier
```

## ⚙️ Configuration

### 📁 Fichier `config/metiers.yaml`

```yaml
metiers:
  thematiques:
    vente-commerce:
      slug: "vente-commerce"
      title_fr: "Vente & Commerce"
      title_en: "Sales & Commerce"
      description_fr: "Formations pour devenir commercial..."
      keywords: ["vente", "vendeur", "commercial"]
      category_ids: []
```

### 🎛️ Feature Flag

```yaml
features:
  metiers_enabled: true  # false pour désactiver
```

## 🎨 Thématiques disponibles

1. **Vente & Commerce** (`/metiers/vente-commerce`)
   - Mots-clés : vente, vendeur, commercial, management, négociation
   
2. **Développement Web** (`/metiers/developpement-web`)
   - Mots-clés : wordpress, html, css, développeur web, javascript
   
3. **Trading & Finance** (`/metiers/trading-finance`)
   - Mots-clés : trading, bourse, matières premières, spread, portefeuille

## 🔍 Fonctionnement du filtrage

Le service `MetierService` filtre les formations selon :

1. **Categories IDs** : Correspondance exacte avec `formation.category.id`
2. **Mots-clés** : Recherche dans `nameFormation`, `descriptionFormation`, `presentation`
3. **Logique OR** : Une formation apparaît si elle correspond à AU MOINS un critère

## 🌍 Internationalisation

### 🇫🇷 Français (défaut)
- Contenu provient directement de la base de données
- Slugs et URLs en français

### 🇬🇧 Anglais 
- Toggle en haut à droite des pages métiers
- Traduction automatique des contenus BDD si pas de version EN
- Badge "Traduction automatique" affiché

### 🔄 Bascule de langue
```twig
<a href="{{ path('app_metiers_switch_locale', {'targetLocale': 'en'}) }}">
    🇬🇧 EN
</a>
```

## 🔗 Routes disponibles

| Route | URL | Description |
|-------|-----|-------------|
| `app_metiers_index` | `/metiers` | Page hub des métiers |
| `app_metiers_show` | `/metiers/{slug}` | Détail d'un métier |
| `app_metiers_switch_locale` | `/metiers/lang/{locale}` | Bascule de langue |

## 📊 SEO & Performances

### 🏷️ Balises Meta
- Title optimisé : "Métier {Thématique} — Formations pour devenir {Métier}"
- Meta description ~155 caractères avec mots-clés
- Canonical URL et hreflang FR/EN

### 📋 JSON-LD Schema
- **Page Hub** : `ItemList` des thématiques
- **Page Métier** : `ItemList` avec `Course` pour chaque formation
- Données structurées Google-friendly

### 🚀 Performance
- CSS inline optimisé
- Images lazy-loading ready
- Grid CSS responsive
- Mobile-first approach

## 🧪 Tests

### ✅ Tests unitaires
```bash
# Service MetierService
php bin/phpunit tests/Service/MetierServiceTest.php

# Controller MetierController  
php bin/phpunit tests/Controller/MetierControllerTest.php
```

### ✅ Tests HTTP
```bash
# Page hub métiers
curl -I https://votresite.com/metiers

# Page métier spécifique
curl -I https://votresite.com/metiers/vente-commerce
```

## 🛠️ Maintenance

### ➕ Ajouter une nouvelle thématique

1. **Éditer** `config/metiers.yaml`
2. **Ajouter** la configuration complète :
```yaml
nouvelle-thematique:
  slug: "nouvelle-thematique"
  title_fr: "Nouveau Métier"
  title_en: "New Career"
  description_fr: "Description..."
  keywords: ["mot1", "mot2"]
  category_ids: [1, 2]
```
3. **Mettre à jour** le menu dans `templates/base.html.twig`
4. **Tester** que des formations correspondent aux critères

### 🔧 Modifier les critères de filtrage

Éditer la méthode `findFormationsByMetier()` dans `MetierService.php`

### 🎨 Personnaliser l'apparence

Les styles sont dans les templates Twig :
- `templates/metiers/index.html.twig` (page hub)
- `templates/metiers/show.html.twig` (page détail)

## 🚨 Rollback

### 🔄 Désactivation rapide
```yaml
# config/metiers.yaml
features:
  metiers_enabled: false
```

### ⬅️ Rollback complet
```bash
# Retour arrière Git
git revert <commit-hash>

# Ou commentaire du menu
# templates/base.html.twig ligne 2466-2482
{# Menu Métiers #}
```

## 📈 Impact SEO attendu

1. **Nouvelles pages indexables** : +3 pages thématiques + 1 page hub
2. **Mots-clés longue traîne** : "formation vente", "devenir développeur web"
3. **Structure en silo** : Regroupement thématique améliore la pertinence
4. **Maillage interne** : Liens croisés entre formations similaires
5. **JSON-LD** : Améliore la compréhension par les moteurs

## 🔒 Sécurité

- ✅ **Lecture seule** : Aucune modification en base de données
- ✅ **Validation** : Contrôle des slugs et paramètres d'entrée
- ✅ **404 propres** : Gestion des erreurs pour métiers inexistants
- ✅ **Feature flag** : Contrôle d'activation centralisé

## 📞 Support

Pour toute question ou modification :
1. Vérifier cette documentation
2. Consulter les logs Symfony (`var/log/`)
3. Tester les routes avec `php bin/console debug:router`
4. Vérifier la config avec `php bin/console debug:config`
