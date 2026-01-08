# INFPF - Changelog Decembre 2025

## Resume des modifications

Ce document recapitule les modifications apportees au projet INFPF durant le mois de decembre 2025.

---

## 1. Page d'accueil

### Systeme d'accordeon pour les formations

- Ajout des boutons "Tout deplier" et "Tout replier" pour la section formations
- Implementation du systeme de pliage/depliage des categories de formation
- Chaque categorie peut etre ouverte ou fermee individuellement
- Les boutons se desactivent automatiquement selon l'etat

### Adaptations mobile

- Boutons accordeon adaptes pour les petits ecrans
- Repositionnement de la fleche d'expansion sur mobile
- Ajustement des tailles et centrage des boutons sur mobile

### Icones de contact

- Remplacement des cercles vides par des icones SVG significatives :
  - Horloge pour "Reponse sous 24h"
  - Coche pour "Bilan personnalise"
  - Etoile pour "Conseil expert"
  - Graphique pour "Suivi carriere"

### Corrections CSS

- Ajustement des overflow pour permettre le scroll vertical
- Correction du border-radius sur les images de categorie
- Reduction du padding sur les cartes hero et formulaire
- Correction de l'effet hover sur les cartes de formation

---

## 2. Page Formation

### Bouton "Voir le programme"

- Couleur du texte forcee en blanc pour meilleure lisibilite
- Ajout d'une bordure blanche semi-transparente
- Fleche de navigation egalement en blanc
- Bordure plus visible au survol

---

## 3. Page DataDock

### Correction critique

- Suppression de plus de 400 lignes de code duplique
- Resolution de l'erreur 500 qui bloquait l'acces a la page

---

## 4. Pages CGV, Disclaimer et Avis

### Suppression des animations hover

Les animations au survol ont ete supprimees sur les elements non-cliquables :

- CGV : Suppression de l'effet de levitation des sections
- Disclaimer : Suppression de l'effet de levitation des cartes
- Avis : Suppression de l'effet de levitation des cartes de temoignages

---

## Resume technique

| Fichier | Type de modification | Impact |
|---------|---------------------|--------|
| home.html.twig | Nouvelles fonctionnalites | Systeme accordeon formations |
| formation.html.twig | Style CSS | Bouton plus lisible |
| infpf-reference-datadock.html.twig | Correction bug | Page accessible |
| cgv.html.twig | Suppression animations | UX amelioree |
| disclaimer.html.twig | Suppression animations | UX amelioree |
| avis.html.twig | Suppression animations | UX amelioree |

---

Date des modifications : Decembre 2025
