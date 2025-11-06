# 📊 INTÉGRATION GOOGLE ANALYTICS 4 - Guide Rapide

**Objectif** : Activer Google Analytics une fois l'ID de mesure obtenu  
**Durée** : 10 minutes

---

## 🎯 PRÉREQUIS

Vous devez avoir :
- ✅ Compte Google Analytics créé
- ✅ Propriété GA4 configurée
- ✅ **ID de mesure** (format : `G-XXXXXXXXXX`)

**Si pas encore fait** : Suivez `JOUR3_GUIDE_IMPLEMENTATION.md` → PARTIE 2

---

## 🚀 ÉTAPE 1 : Configuration Variables d'Environnement (2 min)

### 1.1 Ajouter dans `.env.local`

```bash
cd /home/u665392393/domains/infpf.fr/dev
nano .env.local
```

**Ajoutez ces lignes** :
```env
###> Google Analytics ###
GOOGLE_ANALYTICS_ENABLED=true
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
###< Google Analytics ###
```

**Remplacez `G-XXXXXXXXXX`** par votre vrai ID de mesure !

**Sauvegardez** : `Ctrl+O` → `Entrée` → `Ctrl+X`

---

### 1.2 Ajouter dans `.env` (Production)

**Même chose sur le serveur de production** :
```bash
cd /home/u665392393/domains/infpf.fr/public_html
nano .env.local
```

Ajoutez les mêmes lignes avec votre ID réel.

---

## 🚀 ÉTAPE 2 : Configuration Symfony (3 min)

### 2.1 Créer le fichier de config

```bash
cd /home/u665392393/domains/infpf.fr/dev
nano config/packages/analytics.yaml
```

**Contenu** :
```yaml
parameters:
    analytics.enabled: '%env(bool:GOOGLE_ANALYTICS_ENABLED)%'
    analytics.google.measurement_id: '%env(GOOGLE_ANALYTICS_ID)%'
```

**Sauvegardez** : `Ctrl+O` → `Entrée` → `Ctrl+X`

---

### 2.2 Mettre à jour `config/services.yaml`

```bash
nano config/services.yaml
```

**Ajoutez dans la section `services:` (après `_defaults:`)** :

```yaml
services:
    _defaults:
        # ... existing config ...
    
    # Inject analytics parameters globally
    Twig\Extension\AbstractExtension:
        calls:
            - setDefaultContext:
                - analytics_enabled: '%analytics.enabled%'
                - ga_measurement_id: '%analytics.google.measurement_id%'
```

**OU simplement ajoutez les variables globales Twig** :

```yaml
# Dans twig.yaml
twig:
    globals:
        analytics_enabled: '%analytics.enabled%'
        ga_measurement_id: '%analytics.google.measurement_id%'
```

**Sauvegardez** : `Ctrl+O` → `Entrée` → `Ctrl+X`

---

## 🚀 ÉTAPE 3 : Intégrer dans le Template (5 min)

### 3.1 Modifier `templates/base.html.twig`

```bash
nano templates/base.html.twig
```

**Trouvez la balise `<head>`** (ligne ~5) et **juste avant `</head>`**, ajoutez :

```twig
{# Google Analytics 4 #}
{% include 'analytics/google-analytics.html.twig' %}
```

**Exemple complet** :
```twig
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {# ... vos autres balises meta, CSS, etc. ... #}
    
    {# Google Analytics 4 - Avant </head> #}
    {% include 'analytics/google-analytics.html.twig' %}
</head>
```

**Sauvegardez** : `Ctrl+O` → `Entrée` → `Ctrl+X`

---

## 🚀 ÉTAPE 4 : Clear Cache (1 min)

```bash
cd /home/u665392393/domains/infpf.fr/dev
php bin/console cache:clear
php bin/console cache:warmup
```

---

## 🚀 ÉTAPE 5 : Test (5 min)

### 5.1 Vérifier le Code Source

**Ouvrez votre site** :
```
https://dev.infpf.fr
```

**Faites un clic droit → "Afficher le code source"**

**Cherchez** (Ctrl+F) : `gtag`

**Vous devez voir** :
```html
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  
  gtag('config', 'G-XXXXXXXXXX', {
    'anonymize_ip': true,
    ...
  });
</script>
```

✅ **Si vous voyez ça = Code intégré !**

---

### 5.2 Test Temps Réel dans Google Analytics

1. **Ouvrez Google Analytics** :
   ```
   https://analytics.google.com/
   ```

2. **Allez dans "Rapports → Temps réel"**

3. **Dans un autre onglet, ouvrez** :
   ```
   https://dev.infpf.fr
   ```

4. **Retournez sur Google Analytics**

**Vous devez voir** :
```
Utilisateurs actifs : 1
Page active : /
Pays : France
```

✅ **Si vous voyez 1 utilisateur = Tracking OK !**

---

### 5.3 Test Extension Google Tag Assistant (Optionnel)

1. **Installez l'extension Chrome** :
   ```
   https://chrome.google.com/webstore/detail/tag-assistant-companion/jmekfmbnaedfebfnmakmokmlfpblbfdm
   ```

2. **Ouvrez votre site** → **Cliquez sur l'icône Tag Assistant**

**Vous devez voir** :
- ✅ **Google Analytics 4** - Tag firing correctly
- ID : `G-XXXXXXXXXX`

---

## 🚀 ÉTAPE 6 : Tracker les Événements Personnalisés (Optionnel - 10 min)

### 6.1 Formulaire de Contact

**Dans votre fichier JS du formulaire contact** (ex: `public/js/contact-form.js`) :

**Après l'envoi réussi du formulaire, ajoutez** :

```javascript
// Après succès de l'envoi
if (response.success) {
    // ... votre code existant ...
    
    // Tracker l'événement dans GA4
    var event = new CustomEvent('formSubmitSuccess', {
        detail: {
            formName: 'contact',
            formLocation: window.location.pathname
        }
    });
    document.dispatchEvent(event);
}
```

Le code dans `google-analytics.html.twig` capturera automatiquement cet événement !

---

### 6.2 Boutons CTA avec Tracking

**Pour tracker un bouton spécifique**, ajoutez l'attribut `data-ga-event` :

```html
<a href="/formations" 
   class="btn btn-primary" 
   data-ga-event='{"action":"cta_click","category":"engagement","label":"voir_formations","name":"btn_formations"}'>
    Voir les Formations
</a>
```

Le tracking est **automatique** grâce au code dans `google-analytics.html.twig` !

---

## ✅ VÉRIFICATION FINALE

**Checklist** :
- [ ] Variables d'environnement ajoutées (`.env.local`)
- [ ] Configuration Symfony créée (`analytics.yaml`)
- [ ] Variables globales Twig configurées
- [ ] Template inclus dans `base.html.twig`
- [ ] Cache Symfony cleared
- [ ] Code visible dans le source HTML
- [ ] Test temps réel : 1 utilisateur visible dans GA4
- [ ] Extension Tag Assistant : Tag OK

**Si tout est coché** : ✅ **Google Analytics 4 opérationnel !**

---

## 📊 ÉVÉNEMENTS TRACKÉS AUTOMATIQUEMENT

Une fois intégré, ces événements sont **automatiquement trackés** :

1. ✅ **Pages vues** (toutes les pages)
2. ✅ **Formulaire contact** (via CustomEvent)
3. ✅ **Clics sur boutons CTA** (avec `data-ga-event`)
4. ✅ **Téléchargements** (PDF, DOC, ZIP)
5. ✅ **Scroll depth** (25%, 50%, 75%, 100%)
6. ✅ **Temps sur page** (30s, 1min, 2min, 5min)
7. ✅ **Recherche** (si formulaire de recherche présent)

**Aucun code supplémentaire requis !** 🎉

---

## 🎯 PROCHAINES ÉTAPES

**Après 24-48h**, vous aurez accès à :
- 📊 Rapports détaillés (pages populaires)
- 🌍 Données démographiques (pays, villes)
- 🔗 Sources de trafic (Google, direct, réseaux sociaux)
- 🎯 Taux de conversion (formulaire)
- 📈 Comportement utilisateur (parcours)

**Allez dans** : `Google Analytics → Rapports`

---

## ❓ TROUBLESHOOTING

### Problème : Le code n'apparaît pas dans le HTML

**Solution** :
1. Vérifiez que `GOOGLE_ANALYTICS_ENABLED=true` dans `.env.local`
2. Vérifiez que vous êtes en environnement `prod` (le code ne s'active qu'en prod)
3. Clear le cache : `php bin/console cache:clear`

---

### Problème : Aucun utilisateur dans Temps Réel

**Solutions** :
1. Attendez 1-2 minutes (délai normal)
2. Désactivez les bloqueurs de pub (uBlock, AdBlock)
3. Vérifiez l'ID de mesure dans `.env.local`
4. Vérifiez dans la console navigateur (F12) : pas d'erreur JS ?

---

### Problème : Événements personnalisés ne fonctionnent pas

**Solution** :
1. Vérifiez que le code JavaScript est bien exécuté (console F12)
2. Vérifiez que l'événement `CustomEvent` est bien dispatché
3. Testez dans `Google Analytics → Temps réel → Événements`

---

## 📞 BESOIN D'AIDE ?

**Donnez-moi** :
1. Votre ID de mesure (ex: `G-ABC123DEF4`)
2. L'erreur exacte (capture d'écran ou message)
3. Console navigateur (F12 → Console)

Je vous aiderai à débugger ! 🚀

---

**Date** : 6 novembre 2025  
**Branche** : `feature/performance-security-seo-optimization`  
**Fichiers créés** :
- `templates/analytics/google-analytics.html.twig` : Code de tracking
- `config/packages/analytics.yaml` : Configuration (à créer)
- Ce guide : `GOOGLE_ANALYTICS_INTEGRATION.md`





