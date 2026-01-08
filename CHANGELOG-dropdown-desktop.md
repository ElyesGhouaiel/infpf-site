# Modifications du dropdown "Selectionnez votre domaine de formation" - Desktop

## Date: 8 janvier 2026

### Modifications apportees

#### 1. Layout en grille 2 colonnes (Desktop)
- Affichage des 9 categories en 2 colonnes sur desktop (min-width: 992px)
- 4 categories par colonne, la 9eme categorie "Autres" centree en bas
- Espacement de 20px entre chaque categorie
- Desactivation du centrage de "Autres" sur mobile (meme largeur pour toutes)

#### 2. Badge "NEW" pour la categorie IA
- Badge reduit et repositionne (anciennement "NOUVEAU")
- Position en haut a droite de la categorie
- Taille compacte avec animation pulse

#### 3. Fleche d'accordeon amelioree
- Couleur blanche avec trait plus epais (stroke-width: 3.5)
- Fond semi-transparent en cercle
- Taille agrandie (38px)
- Ombre portee pour meilleure visibilite
- Animation de rotation au deploiement

#### 4. Suppression du fond bleu sur le titre
- Retrait du background sur `.category-title` dans le dropdown uniquement
- Conservation du fond bleu sur les cartes de la section categories

#### 5. Effet de survol sur les categories (Desktop)
- Grossissement au survol (scale 1.03)
- Ombre renforcee au hover
- Transition fluide (0.3s)
- Effet uniquement sur desktop

#### 6. Correction des icones
- Desactivation de l'effet `::before` sur `.category-title` dans la grille
- Resolution du probleme de debordement des icones

### Fichiers modifies
- `templates/home/home.html.twig`

### Notes techniques
- Media query desktop: min-width: 992px
- Media query mobile: max-width: 991px
- Utilisation de CSS Grid pour la disposition en 2 colonnes
- Utilisation de `!important` pour surpasser les styles inline JS
