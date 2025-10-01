# Pipeline Contenu Formation - Documentation BDD → UI

## 📋 Vue d'ensemble

Ce document détaille **comment les contenus textuels des formations sont saisis, stockés et affichés** dans l'application INFPF, depuis la base de données jusqu'à l'interface utilisateur.

## 🗃️ Structure Base de Données

### Table `formation`

Les contenus textuels sont stockés dans les colonnes suivantes :

| Champ | Type | Taille | Description | Format attendu |
|-------|------|---------|------------|----------------|
| `presentation` | TEXT | 5,000 | Présentation générale de la formation | Texte libre avec retours à la ligne |
| `prerequis` | TEXT | 5,000 | Conditions préalables | Liste à puces (• ou -) recommandée |
| `atouts` | TEXT | 5,000 | Points forts de la formation | Liste à puces (• ou -) recommandée |
| `programme` | TEXT | 50,000 | Programme détaillé | Texte structuré avec sections |
| `modalites_pedagogique` | TEXT | 5,000 | Méthodes pédagogiques | Texte libre avec retours à la ligne |
| `evaluation` | TEXT | 5,000 | Modalités d'évaluation | Texte libre avec retours à la ligne |

### Localisation des fichiers

- **Entité** : `src/Entity/Formation.php`
- **Formulaire** : `src/Form/FormationType.php`  
- **Template** : `templates/content/formation/show.html.twig`

## ✏️ Saisie des Données

### Interface d'administration

Les contenus sont saisis via le formulaire Symfony défini dans `FormationType.php` :

```php
// Champs configurés comme TextareaType pour permettre le texte long
->add('presentation', TextareaType::class, [...])
->add('prerequis', TextareaType::class, [...])
->add('atouts', TextareaType::class, [...])
->add('programme', TextareaType::class, [...])
```

### Bonnes pratiques de saisie

#### ✅ Format recommandé pour les listes (Pré-requis, Atouts)
```
• Premier élément de la liste
• Deuxième élément  
• Troisième élément

Ou utiliser des tirets :

- Premier élément
- Deuxième élément
- Troisième élément
```

#### ✅ Format recommandé pour le programme
```
• Partie 1 : Introduction
Description de la première partie...

• Partie 2 : Développement  
Description de la deuxième partie...

• Partie 3 : Conclusion
Description finale...
```

#### ✅ Format pour les textes longs
```
Premier paragraphe avec description générale.

Deuxième paragraphe après une ligne vide.

Troisième paragraphe avec des informations complémentaires.
```

#### ❌ À éviter
- Texte en une seule ligne très longue
- Mélange de formats (HTML + texte brut)
- Caractères spéciaux non nécessaires

## 🔄 Pipeline de Traitement

### 1. Stockage (BDD)
```
Formation Entity → Doctrine ORM → MySQL/PostgreSQL
↓
Stockage en tant que TEXT brut (pas d'HTML)
```

### 2. Récupération (Backend)
```
FormationRepository → FormationController → Template
↓
Passage direct des données sans transformation
```

### 3. Affichage (Frontend)
```
Template Twig → Filtres de formatage → HTML final
↓
Transformation automatique : texte brut → HTML structuré
```

## 🎨 Système de Formatage

### Filtres Twig développés

Le système utilise des **filtres Twig personnalisés** dans `src/Twig/TextFormatterExtension.php` :

#### `format_formation_text`
- **Objectif** : Convertir le texte brut en HTML structuré
- **Traitement** :
  - Détection automatique des listes à puces (•, -, *)
  - Séparation des paragraphes sur les doubles retours à la ligne
  - Échappement HTML pour la sécurité
  - Préservation stricte du contenu original

#### `format_list_text`  
- **Objectif** : Traitement spécialisé pour les listes
- **Traitement** :
  - Conversion des puces en `<ul><li>`
  - Gestion mixte listes + paragraphes
  - Préservation de l'ordre et du contenu

### Exemple de transformation

#### Texte saisi (BDD)
```
• Prérequis technique : connaissance de base
• Motivation pour apprendre
• Disponibilité de 2h par semaine

Cette formation s'adresse à tous les niveaux.
```

#### HTML généré (Frontend)  
```html
<ul>
  <li>Prérequis technique : connaissance de base</li>
  <li>Motivation pour apprendre</li>
  <li>Disponibilité de 2h par semaine</li>
</ul>

<p>Cette formation s'adresse à tous les niveaux.</p>
```

## 🎯 Amélirations UX Appliquées

### CSS Optimisé
```css
.formation-text-content {
    max-width: 70ch;        /* Largeur de lecture optimale */
    font-size: 1.1rem;      /* Taille lisible */
    line-height: 1.7;       /* Espacement vertical confortable */
    color: var(--text-primary);
}

.formation-text-content p {
    margin: 0 0 1.2rem 0;   /* Espacement entre paragraphes */
}

.formation-text-content ul {
    margin: 0 0 1.2rem 1.5rem; /* Indentation et espacement listes */
}

.formation-text-content li {
    margin: 0 0 0.6rem 0;   /* Espacement entre éléments de liste */
    line-height: 1.6;
}
```

### Avantages UX obtenus
- ✅ **Lisibilité améliorée** : max-width 70ch, line-height 1.7
- ✅ **Structure claire** : listes automatiques, paragraphes séparés  
- ✅ **Cohérence visuelle** : formatage uniforme sur toutes les sections
- ✅ **Accessibilité** : structure HTML sémantique
- ✅ **Sécurité** : échappement HTML automatique

## 🛡️ Sécurité

### Échappement XSS
- Tous les contenus sont échappés via `htmlspecialchars()`
- Utilisation du flag `|raw` uniquement sur du HTML généré côté serveur
- Aucun HTML brut depuis la BDD n'est affiché directement

### Validation
- Taille maximale respectée (5,000 / 50,000 caractères)
- Pas d'exécution de code côté client
- Filtrage des caractères dangereux

## 🔧 Maintenance et Évolution

### Modifier le formatage
1. **Filtres Twig** : `src/Twig/TextFormatterExtension.php`
2. **Styles CSS** : Section `/* ===== FORMATAGE TEXTE OPTIMISÉ ===== */` dans le template
3. **Template** : `templates/content/formation/show.html.twig`

### Ajouter un nouveau type de formatage
1. Créer une nouvelle méthode dans `TextFormatterExtension`
2. Déclarer le filtre dans `getFilters()`
3. Utiliser dans le template : `{{ content|nouveau_filtre|raw }}`

### Debug et tests
```bash
# Vider le cache Twig après modification des filtres
php bin/console cache:clear

# Tester sur une formation spécifique  
# Aller sur /formation/{id} et vérifier l'affichage
```

## 📝 Checklist Édition de Contenu

### Avant de saisir du contenu
- [ ] Séparer les idées par des retours à la ligne
- [ ] Utiliser • ou - pour les listes
- [ ] Double retour à la ligne pour séparer les paragraphes
- [ ] Éviter les phrases trop longues (max 2-3 lignes)

### Après saisie
- [ ] Prévisualiser sur `/formation/{id}`
- [ ] Vérifier que les listes s'affichent correctement
- [ ] Contrôler que les paragraphes sont bien séparés
- [ ] Tester l'affichage mobile

## 🚀 Performance

### Optimisations appliquées
- Filtres Twig mis en cache automatiquement
- Génération HTML côté serveur (pas de JavaScript)
- CSS optimisé avec classes réutilisables
- Pas de requêtes supplémentaires (traitement en mémoire)

---

**Documentation mise à jour le** : {{ "now"|date("d/m/Y") }}  
**Auteur** : Assistant AI  
**Version** : 1.0








