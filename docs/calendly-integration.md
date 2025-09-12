# Intégration Calendly Optimisée - Documentation

## Vue d'ensemble

Le système Calendly a été entièrement refactorisé pour offrir une expérience utilisateur optimale et une maintenance simplifiée.

## Fonctionnalités principales

### ✅ Système centralisé et unifié
- Gestion centralisée dans `base.html.twig`
- Auto-découverte des boutons
- Chargement optimisé et asynchrone
- Gestion d'erreurs robuste avec fallback

### ✅ Améliorations UX/UI
- Animations fluides et micro-interactions
- Design moderne avec gradients
- Responsive design
- États de chargement visuels
- Accessibilité améliorée (ARIA, clavier)

### ✅ Performance optimisée
- Préchargement intelligent
- Détection et évitement des doublons
- Gestion mémoire optimisée
- Observateur de mutations pour le contenu dynamique

### ✅ Fiabilité renforcée
- Fallback vers redirection externe
- Timeouts et retry automatiques
- Notifications utilisateur
- Support tracking analytics

## Utilisation

### Méthode 1 : Classes CSS automatiques
```html
<!-- Bouton simple -->
<a class="calendly-btn" href="#" data-calendly-url="https://calendly.com/contact-infpf?locale=fr">
    📅 Prendre rendez-vous
</a>

<!-- Bouton avec variantes -->
<a class="calendly-btn calendly-btn--large" href="#" data-calendly-url="...">
    📞 Appel gratuit
</a>

<a class="calendly-btn calendly-btn--outline calendly-btn--small" href="#" data-calendly-url="...">
    💬 Conseil rapide
</a>
```

### Méthode 2 : Composant Twig réutilisable
```twig
{# Bouton simple #}
{{ include('components/calendly_button.html.twig', {
    text: 'Réserver un appel',
    icon: '📞'
}) }}

{# Bouton avec section complète #}
{{ include('components/calendly_button.html.twig', {
    title: 'Besoin d\'aide ?',
    subtitle: 'Échangez avec nos conseillers',
    text: 'Prendre rendez-vous',
    size: 'large',
    icon: '🎯'
}) }}
```

## Classes CSS disponibles

### Tailles
- `.calendly-btn--small` : Bouton compact
- `.calendly-btn` : Taille standard 
- `.calendly-btn--large` : Bouton mis en avant

### Variantes
- `.calendly-btn` : Style par défaut (bleu dégradé)
- `.calendly-btn--outline` : Style contour transparent

### États automatiques
- `.loading` : Animation de chargement
- `:hover` : Effet au survol
- `:focus` : Contour d'accessibilité
- `:active` : Feedback tactile

## Configuration

### URL par défaut
```javascript
defaultUrl: 'https://calendly.com/contact-infpf?locale=fr'
```

### Paramètres configurables
```javascript
window.CalendlyManager.config = {
    defaultUrl: 'https://calendly.com/contact-infpf?locale=fr',
    loadTimeout: 10000,
    retryAttempts: 3
}
```

## Sélecteurs automatiquement détectés

Le système détecte automatiquement ces sélecteurs :
- `.calendly-btn`
- `[data-calendly-url]`
- `.btn-calendly-contact`
- `.btn-calendly-footer`
- `.btn-calendly-detail`
- `.calendly-trigger`
- `#cta-rdv`

## API JavaScript

### Ouverture manuelle
```javascript
// Utiliser l'URL par défaut
window.CalendlyManager.openPopup();

// Avec URL personnalisée
window.CalendlyManager.openPopup('https://calendly.com/specific-calendar');

// Avec bouton pour animation
const button = document.querySelector('.mon-bouton');
window.CalendlyManager.openPopup(null, button);
```

### Enregistrement manuel
```javascript
const monBouton = document.querySelector('.custom-button');
window.CalendlyManager.registerButton(monBouton);
```

## Fonctionnalités avancées

### Tracking Analytics
```javascript
// Automatique si gtag est disponible
gtag('event', 'calendly_popup_opened', {
    event_category: 'engagement',
    event_label: url
});
```

### Fallback robuste
En cas d'échec du popup Calendly :
1. Ouverture dans une nouvelle fenêtre
2. Notification utilisateur automatique
3. URL ajustée avec domaine d'origine

### Gestion des erreurs
- Timeouts configurables
- Messages d'erreur explicites
- Fallback automatique
- Retry intelligent

## Migration depuis l'ancien système

### ✅ Supprimé automatiquement
- Scripts Calendly dupliqués
- CSS redondants
- Event listeners manuels
- Gestion d'erreurs basique

### ✅ Remplacé par
- Système centralisé intelligent
- CSS unifiés et optimisés
- Auto-découverte des boutons
- Gestion d'erreurs avancée

## Bonnes pratiques

### 🎯 Recommandations
1. Utiliser le composant Twig pour la cohérence
2. Préférer `data-calendly-url` aux onclick
3. Ajouter des titres/sous-titres descriptifs
4. Tester le fallback sur différents navigateurs
5. Vérifier l'accessibilité clavier

### ⚠️ À éviter
1. Charger Calendly manuellement
2. Dupliquer les event listeners
3. Utiliser onclick inline
4. Oublier les attributs d'accessibilité

## Support et débogage

### Console de débogage
```javascript
// Vérifier l'état du système
console.log(window.CalendlyManager);

// Forcer le rechargement
window.CalendlyManager.loadCalendly();

// Lister les boutons enregistrés
console.log(window.CalendlyManager.buttons);
```

### Tests de fonctionnement
1. Vérifier le chargement de Calendly
2. Tester l'ouverture des popups
3. Valider le fallback en cas d'erreur
4. Contrôler l'accessibilité
5. Vérifier la responsivité

## Maintenance

### Ajout de nouveaux boutons
Le système détecte automatiquement les nouveaux boutons ajoutés dynamiquement grâce au `MutationObserver`.

### Mise à jour de l'URL
Modifier `window.CalendlyManager.config.defaultUrl` ou utiliser `data-calendly-url` sur les boutons individuels.

### Performance monitoring
Le système inclut des métriques automatiques :
- Temps de chargement Calendly
- Taux de succès/échec
- Utilisation du fallback

---

**Version** : 2.0  
**Dernière mise à jour** : Janvier 2025  
**Compatibilité** : Tous navigateurs modernes + fallback IE11 