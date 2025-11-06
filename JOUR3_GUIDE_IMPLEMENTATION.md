# 🚀 JOUR 3 : MONITORING & ANALYTICS

**Date** : 6 novembre 2025  
**Durée estimée** : 2-3 heures  
**Objectif** : Surveillance 24/7 + Statistiques visiteurs

---

## 📋 PLAN DU JOUR 3

1. **UptimeRobot** (1h) : Surveillance disponibilité 24/7
2. **Google Analytics 4** (1-2h) : Statistiques visiteurs en temps réel

---

# 🔔 PARTIE 1 : UPTIMEROBOT (1h)

## 🎯 Objectif
Être alerté immédiatement si votre site devient inaccessible (serveur down, DNS problème, etc.)

## 📊 Ce Que Vous Aurez
- ✅ Surveillance toutes les 5 minutes (gratuit)
- ✅ Alertes email/SMS si site down > 2 min
- ✅ Dashboard uptime (ex: 99.95%)
- ✅ Historique des incidents
- ✅ Page de statut publique (optionnel)

---

## 🚀 ÉTAPE 1 : Créer Compte UptimeRobot (5 min)

### 1.1 Inscription
```
👉 https://uptimerobot.com/signUp
```

**Remplissez** :
- Email : `elyes@xeilos.fr`
- Mot de passe : (choisissez un mot de passe fort)
- Cochez "I agree to the terms"
- Cliquez "Sign Up"

### 1.2 Vérifier Email
- Ouvrez votre boîte `elyes@xeilos.fr`
- Cliquez sur le lien de confirmation
- Connectez-vous

---

## 🚀 ÉTAPE 2 : Créer les Monitors (15 min)

### 2.1 Monitor Homepage
```
👉 Cliquez "+ Add New Monitor" (bouton orange)
```

**Configuration** :
```
Monitor Type       : HTTP(s)
Friendly Name      : INFPF - Homepage
URL (or IP)        : https://infpf.fr
Monitoring Interval: 5 minutes (gratuit)
Monitor Timeout    : 30 seconds
```

**Alert Contacts** :
- Email : `elyes@xeilos.fr` (déjà configuré)
- ✅ Cochez "Send notification when UP" (première fois)
- ✅ Cochez "Send notification when DOWN"

**Cliquez "Create Monitor"**

---

### 2.2 Monitor Page Contact
```
👉 Cliquez "+ Add New Monitor"
```

**Configuration** :
```
Monitor Type       : HTTP(s)
Friendly Name      : INFPF - Contact
URL (or IP)        : https://infpf.fr/contactez-nous
Monitoring Interval: 5 minutes
Monitor Timeout    : 30 seconds
```

**Alert Contacts** : `elyes@xeilos.fr` (coché)

**Cliquez "Create Monitor"**

---

### 2.3 Monitor Dev (Environnement de Test)
```
👉 Cliquez "+ Add New Monitor"
```

**Configuration** :
```
Monitor Type       : HTTP(s)
Friendly Name      : INFPF - Dev
URL (or IP)        : https://dev.infpf.fr
Monitoring Interval: 5 minutes
Monitor Timeout    : 30 seconds
```

**Alert Contacts** : `elyes@xeilos.fr` (coché)

**Cliquez "Create Monitor"**

---

## 🚀 ÉTAPE 3 : Configurer les Alertes (10 min)

### 3.1 Ajouter SMS (Optionnel - Payant)
```
👉 Settings → Alert Contacts → Add Alert Contact
```

**Si vous voulez SMS** :
- Type : SMS
- Numéro : Votre mobile (format international : +33...)
- Note : 50 SMS/mois = ~5€/mois

**Recommandation** : Commencez avec email uniquement (gratuit)

---

### 3.2 Configurer Seuil d'Alerte
```
👉 Cliquez sur un monitor → Edit
```

**Scroll vers "Advanced Settings"** :
```
Alert When           : Down
Number of Retries    : 2 retries (avant d'alerter)
Confirmation Retries : 1 retry (confirmation)
```

**Explication** :
- Le site doit être down 2 fois de suite avant alerte
- Évite les faux positifs (coupure réseau temporaire)

**Cliquez "Save Changes"**

---

## 🚀 ÉTAPE 4 : Tester les Alertes (5 min)

### 4.1 Test Email
```
👉 Monitors → Cliquez sur "INFPF - Homepage"
👉 Cliquez "Edit" → Scroll en bas
👉 Cliquez "Send Test Alert"
```

**Vérifiez** :
- Email reçu dans `elyes@xeilos.fr`
- Sujet : "Test alert for INFPF - Homepage"

---

### 4.2 Voir le Dashboard
```
👉 https://uptimerobot.com/dashboard
```

**Vous devez voir** :
```
┌─────────────────────────────────────────┐
│  INFPF - Homepage       ✅ UP (99.99%)  │
│  INFPF - Contact        ✅ UP (99.99%)  │
│  INFPF - Dev            ✅ UP (99.99%)  │
└─────────────────────────────────────────┘

Total Uptime: 99.99%
```

---

## 🚀 ÉTAPE 5 : Page de Statut Publique (Optionnel - 10 min)

### 5.1 Créer la Page
```
👉 Status Pages → Create Status Page
```

**Configuration** :
```
Status Page Name   : INFPF Status
Custom Domain      : status.infpf.fr (optionnel)
Monitors to Show   : ✅ INFPF - Homepage
                     ✅ INFPF - Contact
Visibility         : Public (anyone can view)
```

**Cliquez "Create Status Page"**

---

### 5.2 Obtenir l'URL
```
👉 Copiez l'URL (ex: https://stats.uptimerobot.com/xxxxx)
```

**Vous pouvez** :
- Partager cette URL avec vos utilisateurs
- L'ajouter dans le footer de votre site
- Montrer la transparence sur la disponibilité

---

## ✅ RÉSULTAT FINAL UPTIMEROBOT

**Ce que vous avez maintenant** :
- ✅ 3 monitors actifs (Homepage, Contact, Dev)
- ✅ Surveillance toutes les 5 minutes
- ✅ Alertes email si site down > 2 min
- ✅ Dashboard uptime en temps réel
- ✅ Historique des incidents
- ✅ Page de statut publique (optionnel)

**Coût** : **GRATUIT** (jusqu'à 50 monitors)

---

# 📊 PARTIE 2 : GOOGLE ANALYTICS 4 (1-2h)

## 🎯 Objectif
Comprendre vos visiteurs : combien, d'où, quelles pages, combien de temps, conversions

## 📊 Ce Que Vous Aurez
- ✅ Nombre de visiteurs en temps réel
- ✅ Pages les plus visitées
- ✅ Sources de trafic (Google, direct, réseaux sociaux)
- ✅ Taux de conversion formulaire
- ✅ Comportement utilisateur
- ✅ Données démographiques (pays, ville, langue)

---

## 🚀 ÉTAPE 1 : Créer Compte Google Analytics (10 min)

### 1.1 Inscription
```
👉 https://analytics.google.com/
```

**Connectez-vous** avec votre compte Google (ou créez-en un)

---

### 1.2 Créer une Propriété
```
👉 Cliquez "Commencer" (ou "Create Account" si déjà un compte)
```

**Étape 1 : Nom du compte**
```
Nom du compte : INFPF
Pays         : France
```
→ Cliquez "Suivant"

**Étape 2 : Nom de la propriété**
```
Nom de la propriété : INFPF Site Web
Fuseau horaire      : (GMT+01:00) Paris
Devise              : Euro (EUR)
```
→ Cliquez "Suivant"

**Étape 3 : Détails de l'entreprise**
```
Secteur d'activité  : Éducation / Formation
Taille entreprise   : Petite (1-10 employés)
```
→ Cliquez "Suivant"

**Étape 4 : Objectifs commerciaux**
```
✅ Générer des prospects
✅ Examiner le comportement des utilisateurs
```
→ Cliquez "Créer"

**Acceptez les conditions** → "J'accepte"

---

## 🚀 ÉTAPE 2 : Configurer le Flux de Données (5 min)

### 2.1 Créer un Flux Web
```
👉 "Commencer la collecte de données"
👉 Plateforme : "Web"
```

**Configuration** :
```
URL du site Web : https://infpf.fr
Nom du flux     : Site Web INFPF
```

**✅ Cochez "Activer la mesure améliorée"** (important !)

→ Cliquez "Créer un flux"

---

### 2.2 Obtenir l'ID de Mesure
```
👉 Après création, vous verrez :

ID de mesure : G-XXXXXXXXXX

👉 COPIEZ cet ID (ex: G-ABC123DEF4)
```

**⚠️ GARDEZ CET ID, on va l'utiliser dans le code !**

---

## 🚀 ÉTAPE 3 : Intégration dans le Site (20 min)

**JE VAIS LE FAIRE POUR VOUS !**

Donnez-moi votre **ID de mesure** (ex: `G-ABC123DEF4`) et je vais :
1. Intégrer le code de tracking dans `base.html.twig`
2. Configurer les événements personnalisés :
   - Soumission formulaire contact
   - Clics sur boutons importants
   - Téléchargements
3. Respect RGPD (cookie banner - JOUR 4)

---

## 🚀 ÉTAPE 4 : Vérifier l'Installation (10 min)

### 4.1 Test en Temps Réel
```
👉 Google Analytics → Rapports → Temps réel
```

**Ouvrez dans un autre onglet** :
```
https://infpf.fr
```

**Vous devez voir** :
- 1 utilisateur actif
- Page affichée : "/"
- Pays : France

✅ **Si vous voyez ça = Installation OK !**

---

### 4.2 Extension Google Tag Assistant (Optionnel)
```
👉 https://chrome.google.com/webstore (cherchez "Tag Assistant")
```

**Installez l'extension** → Ouvrez votre site → Cliquez sur l'icône

**Vous verrez** :
- ✅ Google Analytics 4 - Tag firing correctly
- ID : G-XXXXXXXXXX

---

## 🚀 ÉTAPE 5 : Configurer les Événements (15 min)

**JE VAIS LE FAIRE POUR VOUS !**

Je vais configurer ces événements personnalisés :

### 5.1 Événement "Formulaire Contact"
```javascript
gtag('event', 'form_submission', {
  'form_name': 'contact',
  'form_location': '/contactez-nous'
});
```

### 5.2 Événement "Clic Bouton Formation"
```javascript
gtag('event', 'cta_click', {
  'button_name': 'voir_formations',
  'page_location': window.location.href
});
```

### 5.3 Événement "Recherche"
```javascript
gtag('event', 'search', {
  'search_term': 'terme recherché'
});
```

---

## 🚀 ÉTAPE 6 : Lier Google Search Console (10 min)

### 6.1 Ouvrir Search Console
```
👉 https://search.google.com/search-console
```

**Si pas encore configuré** :
1. Cliquez "Ajouter une propriété"
2. Type : "Préfixe URL"
3. URL : `https://infpf.fr`
4. Validation : **Via Google Analytics** (automatique si déjà installé)

---

### 6.2 Lier avec Analytics
```
👉 Google Analytics → Admin → Liens vers les produits
👉 Liens Search Console → "Associer"
👉 Sélectionnez votre propriété → "Confirmer"
```

**Bénéfice** :
- Voir les requêtes Google qui amènent du trafic
- Position moyenne dans les résultats
- Taux de clic (CTR)

---

## 🚀 ÉTAPE 7 : Configurer les Objectifs (15 min)

### 7.1 Créer une Conversion
```
👉 Google Analytics → Admin → Événements
👉 Cliquez "Créer un événement"
```

**Événement : Formulaire Contact Envoyé**
```
Nom de l'événement : form_submit_success
Conditions de correspondance :
  - event_name = form_submission
  - form_name = contact
```

→ Marquer comme "Conversion" ✅

---

### 7.2 Créer un Objectif de Valeur
```
👉 Admin → Conversions → Créer une conversion
```

**Conversion : Lead Qualifié**
```
Nom        : lead_contact
Valeur     : 50€ (valeur estimée d'un lead)
Type       : Formulaire
```

---

## ✅ RÉSULTAT FINAL GOOGLE ANALYTICS

**Ce que vous avez maintenant** :
- ✅ Tracking visiteurs en temps réel
- ✅ Rapports détaillés (pages, sources, durée)
- ✅ Événements personnalisés (formulaire, clics)
- ✅ Conversions trackées
- ✅ Lien avec Search Console
- ✅ Dashboard complet

**Données disponibles après 24-48h** : Rapports complets

---

## 📊 ALTERNATIVE : MATOMO (RGPD-Friendly)

**Si vous préférez Matomo** (hébergé en France, 100% RGPD) :

### Avantages Matomo
- ✅ Données hébergées en France (ou chez vous)
- ✅ 100% conforme RGPD (pas de bannière obligatoire)
- ✅ Pas de partage avec tiers
- ✅ Interface similaire à Google Analytics

### Inconvénients
- ❌ Payant : 19€/mois (Cloud) ou self-hosted
- ❌ Moins de fonctionnalités que GA4
- ❌ Pas d'intégration Search Console

**Recommandation** : Commencez avec **Google Analytics 4** (gratuit), passez à Matomo si RGPD strict requis.

---

## 🎯 RÉCAPITULATIF JOUR 3

| Tâche | Durée | Status |
|-------|-------|--------|
| UptimeRobot - Inscription | 5 min | ⏳ |
| UptimeRobot - 3 Monitors | 15 min | ⏳ |
| UptimeRobot - Alertes | 10 min | ⏳ |
| UptimeRobot - Test | 5 min | ⏳ |
| Google Analytics - Compte | 10 min | ⏳ |
| Google Analytics - Flux | 5 min | ⏳ |
| Google Analytics - Intégration | 20 min | ⏳ |
| Google Analytics - Test | 10 min | ⏳ |
| Google Analytics - Événements | 15 min | ⏳ |
| Google Analytics - Search Console | 10 min | ⏳ |
| Google Analytics - Objectifs | 15 min | ⏳ |

**Total : 2h** (peut être fait en 2 sessions)

---

## ✅ CHECKLIST FINALE

**UptimeRobot** :
- [ ] Compte créé et vérifié
- [ ] 3 monitors configurés (Homepage, Contact, Dev)
- [ ] Alertes email actives
- [ ] Test d'alerte envoyé et reçu
- [ ] Dashboard accessible

**Google Analytics** :
- [ ] Compte créé
- [ ] Propriété GA4 créée
- [ ] ID de mesure obtenu (G-XXXXXXXXXX)
- [ ] Code intégré dans le site
- [ ] Test temps réel : 1 utilisateur visible
- [ ] Événements personnalisés configurés
- [ ] Search Console lié
- [ ] Conversions configurées

---

## 🚀 PROCHAINE ÉTAPE

**Une fois JOUR 3 terminé** → **JOUR 4** :
- Politique de confidentialité
- Mentions légales
- Bannière cookies RGPD

---

**Prêt à commencer ? Suivez les étapes ci-dessus et dites-moi quand vous avez l'ID Google Analytics !** 🎯





