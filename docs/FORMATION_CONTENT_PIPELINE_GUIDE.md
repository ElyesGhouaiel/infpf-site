# Guide - Pipeline Contenu Formation (BDD → UI)

## 📋 Résumé Pipeline

**BDD** → **Entity/ORM** → **Controller** → **Twig Template** → **Custom Filter** → **UI Formatée**

---

## 🗄️ Où Saisir les Données

### Interface d'édition
- **URL Admin** : `/admin` (nécessite ROLE_ADMIN)
- **Section** : Formations → Sélectionner formation → Modifier
- **Formulaire** : `src/Form/FormationType.php`

### Champs de contenu concernés
| Champ BDD | Label Interface | Type | Longueur Max |
|-----------|----------------|------|--------------|
| `presentation` | Présentation | textarea | 65535 chars |
| `prerequis` | Pré-requis | textarea | 65535 chars |
| `atouts` | Atouts | textarea | 65535 chars |
| `programme` | Programme | textarea | 65535 chars |
| `modalites_pedagogique` | Modalités pédagogiques | textarea | 65535 chars |
| `evaluation` | Pédagogie - Évaluation | textarea | 65535 chars |

---

## 📝 Format Attendu (Important !)

### ✅ Format Recommandé : **Plain Text**
- **Pas d'HTML** autorisé en saisie
- **Pas de Markdown** (automatiquement converti)
- **Text brut avec structure via retours ligne et puces**

### Structure reconnue automatiquement :

#### 📋 **Paragraphes**
```
Premier paragraphe de présentation.

Deuxième paragraphe séparé par une ligne vide.
Continuation du même paragraphe sur plusieurs lignes.

Troisième paragraphe.
```

#### 📝 **Listes à puces**
```
• Premier élément de liste
• Deuxième élément
• Troisième élément avec détails

Paragraphe normal après la liste.

- Autre liste avec tirets
- Deuxième élément
- Troisième élément
```

#### 🎯 **Sous-titres automatiques**
```
OBJECTIFS PÉDAGOGIQUES:

• Maîtriser les concepts
• Appliquer en pratique

MÉTHODOLOGIE PROPOSÉE:

Approche progressive basée sur...
```

---

## 🔄 Chemin de Rendu Technique

### 1. **Base de données**
- **Table** : `formation`
- **Stockage** : `TEXT` MySQL (UTF-8)
- **Encoding** : UTF-8 natif

### 2. **Backend (Symfony)**
- **Entity** : `src/Entity/Formation.php`
- **Propriétés** : `$presentation`, `$prerequis`, `$atouts`, etc.
- **Type Doctrine** : `@ORM\Column(type="text")`

### 3. **Controller**
- **Fichier** : `src/Controller/FormationController.php`
- **Action** : `show(Formation $formations)`
- **Pas de traitement** : données brutes passées au template

### 4. **Template & Filtrage**
- **Template** : `templates/content/formation/show.html.twig`
- **Filtre custom** : `{{ formations.presentation|format_formation_text|raw }}`
- **Extension** : `src/Twig/TextFormatterExtension.php`

### 5. **Rendu Final**
- **HTML sémantique** : `<p>`, `<ul><li>`, `<h4>`
- **CSS optimisé lecture** : max-width 65ch, line-height 1.65
- **Responsive** : accordéons mobile pour contenu long

---

## 🔒 Règles de Sanitation (Sécurité XSS)

### Ce qui est préservé ✅
- **Texte intégral** (aucune modification du wording)
- **Retours à la ligne** `\n` → `<br>` ou séparation `<p>`
- **Structure liste** `•/-/*` → `<ul><li>`
- **Caractères spéciaux** : accents, apostrophes, guillemets

### Ce qui est échappé/sécurisé 🛡️
- **Balises HTML** : `<script>` → `&lt;script&gt;`
- **Injection JS** : neutralisée automatiquement
- **Entités doubles** : `&#039;` → `'` (décodage préventif)

### Processus de sanitation
```php
// 1. Normalisation retours ligne
$text = str_replace(["\r\n", "\r"], "\n", $text);

// 2. Décodage préventif (éviter double-échappement)
$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

// 3. Échappement sécurisé unique
$text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
```

---

## 💡 Astuces d'Édition (Anti-Murs de Texte)

### ✅ **Bonnes Pratiques**

#### Structure aérée
- **Paragraphes courts** : 2-4 phrases maximum
- **Ligne vide** entre chaque idée différente
- **Listes** pour énumérations (•) plutôt que phrases longues

#### Exemple BIEN structuré :
```
Cette formation vous permettra de maîtriser les techniques avancées.

Vous apprendrez à :
• Analyser les besoins clients
• Proposer des solutions adaptées  
• Mettre en œuvre les bonnes pratiques

MÉTHODES PÉDAGOGIQUES:

Alternance entre théorie et pratique avec exercices concrets.

La formation se déroule en plusieurs étapes progressives.
```

### ❌ **À Éviter**

#### Mur de texte dense
```
Cette formation vous permettra de maîtriser les techniques avancées de gestion vous apprendrez à analyser les besoins clients, proposer des solutions adaptées, mettre en œuvre les bonnes pratiques, la formation utilise une méthode pédagogique basée sur l'alternance entre théorie et pratique avec des exercices concrets et se déroule en plusieurs étapes progressives...
```

#### Puces redondantes
```
• • Première compétence (double puce)
• - Deuxième compétence (puce mixte)
```

---

## 🧪 Test & Validation

### Environnements de test
1. **Formation #49** : Cas de stress (contenu très dense)
2. **Mobile responsive** : Vérification accordéons
3. **Accessibilité** : Navigation clavier + screen readers

### Validation automatique
- **XSS protection** : Tentatives injection neutralisées
- **Encoding** : Plus d'artefacts `&#039;` visibles  
- **Performance** : Pas de scrollbars internes, scroll fluide

---

## 🚀 Migration Contenu Existant

### Si contenu déjà en HTML
- **Audit nécessaire** : identifier balises custom/inline styles
- **Conversion** : HTML → Plain text avec structure préservée
- **Test** : vérification rendu avec nouveaux filtres

### Si contenu très dense
- **Réorganisation recommandée** :
  1. Séparer en paragraphes courts
  2. Extraire listes du texte continu
  3. Ajouter sous-titres explicites (MAJUSCULES:)

---

## 📞 Support

**Questions techniques** : Équipe développement  
**Questions éditoriales** : Équipe contenu + Référent pédagogique

**Dernière mise à jour** : Septembre 2025