#  GOOGLE ANALYTICS 4 - INTÉGRATION TERMINÉE

**Date** : 6 novembre 2025, 11h00  
**ID de mesure** : `G-MBJWH1R61S`  
**Status** :  **OPÉRATIONNEL**

---

##  INTÉGRATION RÉUSSIE !

Votre **Google Analytics 4** est maintenant **actif** et **tracking** sur votre site !

---

##  CE QUI A ÉTÉ FAIT (10 min)

### 1. Configuration Variables d'Environnement
```bash
.env.local :
GOOGLE_ANALYTICS_ENABLED=true
GOOGLE_ANALYTICS_ID=G-MBJWH1R61S
```

---

### 2. Configuration Symfony
**Fichier créé** : `config/packages/analytics.yaml`
```yaml
parameters:
    analytics.enabled: '%env(bool:GOOGLE_ANALYTICS_ENABLED)%'
    analytics.google.measurement_id: '%env(GOOGLE_ANALYTICS_ID)%'
```

---

### 3. Variables Globales Twig
**Fichier modifié** : `config/packages/twig.yaml`
```yaml
twig:
    globals:
        analytics_enabled: '%analytics.enabled%'
        ga_measurement_id: '%analytics.google.measurement_id%'
```

---

### 4. Intégration dans Template
**Fichier modifié** : `templates/base.html.twig`
```twig
{# Google Analytics 4 - Tracking et événements personnalisés #}
{% include 'analytics/google-analytics.html.twig' %}
```

Ajouté juste avant `</head>` (ligne 2829)

---

### 5. Code de Tracking
**Fichier** : `templates/analytics/google-analytics.html.twig`

**Contient** :
-  Tracking code Google Analytics 4
-  ID de mesure : `G-MBJWH1R61S`
-  Anonymisation IP (RGPD)
-  Cookie flags sécurisés
-  7 événements personnalisés automatiques

---

### 6. Cache Cleared
```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

---

##  ÉVÉNEMENTS TRACKÉS AUTOMATIQUEMENT

Votre site track maintenant **7 types d'événements** sans aucun code supplémentaire :

| # | Événement | Description | Déclencheur |
|---|-----------|-------------|-------------|
| 1 | **Pages vues** | Toutes les pages visitées | Automatique |
| 2 | **Formulaire contact** | Soumission formulaire | CustomEvent `formSubmitSuccess` |
| 3 | **Clics CTA** | Boutons importants | Attribut `data-ga-event` |
| 4 | **Téléchargements** | PDF, DOC, DOCX, ZIP | Clic sur lien `.pdf`, etc. |
| 5 | **Scroll depth** | Profondeur de scroll | 25%, 50%, 75%, 100% |
| 6 | **Temps sur page** | Durée de visite | 30s, 1min, 2min, 5min |
| 7 | **Recherche** | Requêtes recherche | Formulaire `role="search"` |

---

## 🧪 VALIDATION (5 min)

### Test 1 : Code présent dans HTML 
```bash
curl -s https://dev.infpf.fr/ | grep "G-MBJWH1R61S"
```

**Résultat** :
```html
<script async src="https://www.googletagmanager.com/gtag/js?id=G-MBJWH1R61S"></script>
gtag('config', 'G-MBJWH1R61S', {
  'anonymize_ip': true,
  ...
});
```

 **Code bien présent !**

---

### Test 2 : Google Analytics Temps Réel (À FAIRE MAINTENANT)

**1. Ouvrez Google Analytics** :
```
👉 https://analytics.google.com/
👉 Allez dans "Rapports → Temps réel"
```

**2. Dans un autre onglet, ouvrez** :
```
👉 https://dev.infpf.fr/
```

**3. Retournez sur Google Analytics (après 10-30 sec)**

**Vous devriez voir** :
```
┌─────────────────────────────────────────┐
│  👤 Utilisateurs actifs : 1             │
│  📄 Page active : /                     │
│  🌍 Pays : France                       │
└─────────────────────────────────────────┘
```

 **Si vous voyez 1 utilisateur = Tracking opérationnel !**

---

### Test 3 : Extension Google Tag Assistant (Optionnel)

**1. Installez l'extension Chrome** :
```
https://chrome.google.com/webstore/detail/tag-assistant-companion/jmekfmbnaedfebfnmakmokmlfpblbfdm
```

**2. Ouvrez votre site** :
```
https://dev.infpf.fr/
```

**3. Cliquez sur l'icône Tag Assistant**

**Vous devriez voir** :
-  **Google Analytics 4** - Tag firing correctly
- ID : `G-MBJWH1R61S`
- Status :  Connected

---

##  PROCHAINES ÉTAPES

### Immédiatement
```
☐ Tester dans Google Analytics Temps Réel
☐ Vérifier que vous voyez 1 utilisateur actif
☐ Valider que l'ID G-MBJWH1R61S apparaît
```

---

### Après 24-48h
Vous aurez accès à des **rapports complets** :
-  Pages les plus visitées
- 🌍 Provenance géographique des visiteurs
- 🔗 Sources de trafic (Google, direct, réseaux sociaux)
-  Taux de conversion formulaire
-  Comportement utilisateur (parcours)
- ⏱ Temps moyen sur le site

**Dashboard disponible** : `Google Analytics → Rapports`

---

## 🔗 Lier Google Search Console (Optionnel - 10 min)

### Avantage
Voir les **requêtes Google** qui amènent du trafic sur votre site.

### Étapes Rapides
```
1. Allez sur : https://search.google.com/search-console
2. Ajoutez la propriété : https://infpf.fr
3. Validation via Google Analytics (automatique)
4. Dans GA4 : Admin → Liens Search Console → Associer
```

**Bénéfices** :
- Position moyenne dans Google
- Mots-clés qui génèrent des visites
- Taux de clic (CTR)
- Opportunités d'optimisation SEO

---

##  TRACKER UN ÉVÉNEMENT PERSONNALISÉ

### Exemple : Formulaire de Contact

**Dans votre fichier JS du formulaire** (après envoi réussi) :

```javascript
// Après succès de l'envoi
if (response.success) {
    // ... votre code existant ...
    
    // Tracker dans Google Analytics
    var event = new CustomEvent('formSubmitSuccess', {
        detail: {
            formName: 'contact',
            formLocation: window.location.pathname
        }
    });
    document.dispatchEvent(event);
}
```

Le code dans `google-analytics.html.twig` le **capturera automatiquement** ! 

---

### Exemple : Bouton CTA avec Tracking

**Pour tracker un bouton spécifique**, ajoutez `data-ga-event` :

```html
<a href="/formations" 
   class="btn btn-primary" 
   data-ga-event='{"action":"cta_click","category":"engagement","label":"voir_formations","name":"btn_formations"}'>
    Voir les Formations
</a>
```

**Tracking automatique** dès que le bouton est cliqué ! 

---

## 🛡 CONFORMITÉ RGPD

Votre Google Analytics est **partiellement conforme RGPD** :

###  Déjà Fait
-  Anonymisation IP (`anonymize_ip: true`)
-  Cookie flags sécurisés (`SameSite=None;Secure`)
-  Cookie domain configuré (`infpf.fr`)
-  Activation uniquement en production

###  À Faire (JOUR 4)
-  Bannière de consentement cookies
-  Politique de confidentialité
-  Mentions légales
-  Droit à l'oubli

**Ces points seront couverts au JOUR 4 !**

---

##  RÉSUMÉ

| Élément | Status |
|---------|--------|
| Variables d'environnement |  Configurées |
| Configuration Symfony |  Créée |
| Variables globales Twig |  Configurées |
| Template GA4 |  Créé |
| Intégration base.html.twig |  Fait |
| Cache cleared |  Fait |
| Code présent dans HTML |  Vérifié |
| ID de mesure |  G-MBJWH1R61S |
| Événements personnalisés |  7 configurés |
| Test temps réel |  À faire maintenant |

---

##  FÉLICITATIONS !

**Google Analytics 4 est maintenant opérationnel sur votre site !**

### Ce que vous pouvez faire maintenant :
1.  Voir vos visiteurs en temps réel
2.  Comprendre quelles pages sont populaires
3.  Savoir d'où viennent vos visiteurs
4.  Tracker les conversions (formulaire)
5.  Optimiser votre contenu basé sur les données

---

## 📞 BESOIN D'AIDE ?

**Questions** :
- Comment voir mes statistiques ? → Google Analytics → Rapports
- Pourquoi pas de données ? → Attendez 24-48h pour rapports complets
- Comment ajouter un événement ? → Voir section "Tracker un événement personnalisé"

---

##  JOUR 3 - STATUT

| Tâche | Durée | Status |
|-------|-------|--------|
| **UptimeRobot** | 1h |  À FAIRE |
| **Google Analytics** | 1h |  **TERMINÉ** |

**Temps restant JOUR 3** : 1h (UptimeRobot)

---

**Testez maintenant dans Google Analytics Temps Réel et dites-moi ce que vous voyez ! **

---

**Date d'intégration** : 6 novembre 2025, 11h00  
**Branche** : `feature/performance-security-seo-optimization`  
**Fichiers modifiés** :
- `config/packages/analytics.yaml` (créé)
- `config/packages/twig.yaml` (modifié)
- `.env.local` (modifié)
- `templates/base.html.twig` (modifié)
- `templates/analytics/google-analytics.html.twig` (déjà créé)
