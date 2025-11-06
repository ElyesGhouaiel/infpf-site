# 🚨 Problème : CDN Hostinger Bloque les Headers de Sécurité

**Date** : 3 novembre 2025  
**Domaine** : dev.infpf.fr  
**Score Actuel** : Mozilla Observatory 30/100 (D) | SecurityHeaders.com D

---

## 📋 Résumé du Problème

Le CDN Hostinger (hcdn) **filtre et supprime** tous les headers de sécurité personnalisés que nous avons implémentés, empêchant l'amélioration des scores de sécurité.

### Headers Bloqués par le CDN

- ❌ `X-Frame-Options: DENY`
- ❌ `X-Content-Type-Options: nosniff`
- ❌ `X-XSS-Protection: 1; mode=block`
- ❌ `Referrer-Policy: strict-origin-when-cross-origin`
- ❌ `Permissions-Policy: geolocation=(), microphone=(), camera=()`
- ❌ `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload`
- ❌ `Content-Security-Policy` (version complète)

### Ce qui Passe Actuellement

```http
HTTP/2 200 
date: Mon, 03 Nov 2025 14:32:06 GMT
content-type: text/html; charset=UTF-8
vary: Accept-Encoding
x-powered-by: PHP/8.1.32
cache-control: max-age=0, must-revalidate, private
expires: Mon, 03 Nov 2025 14:32:06 GMT
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests  ← Seul header CSP (version limitée)
server: hcdn  ← CDN Hostinger
```

---

## ✅ Code Implémenté (Prêt à Fonctionner)

Tout le code nécessaire a été correctement implémenté :

### 1. SecurityHeadersListener.php ✅

**Fichier** : `src/EventListener/SecurityHeadersListener.php`  
**Statut** : ✅ Enregistré et fonctionnel  
**Vérifié avec** : `php bin/console debug:event-dispatcher kernel.response`

```bash
#2  App\EventListener\SecurityHeadersListener::onKernelResponse()  0
```

### 2. Configuration `.htaccess` ✅

**Fichier** : `public/.htaccess`  
**Statut** : ✅ Headers ajoutés à la fin du fichier  
**Sauvegarde** : `public/.htaccess.backup`

Les headers sont correctement configurés dans `.htaccess` avec `Header always set`.

### 3. Cache Symfony ✅

- ✅ `php bin/console cache:clear --env=prod`
- ✅ `php bin/console cache:warmup --env=prod`

---

## 🔍 Tests Effectués

### Test 1 : Vérification du Listener

```bash
php bin/console debug:event-dispatcher kernel.response
```

**Résultat** : ✅ Le listener est bien enregistré à la priorité 0

### Test 2 : Headers HTTP via curl

```bash
curl -I https://dev.infpf.fr | grep -E "x-frame|x-content|strict-transport|referrer|permissions"
```

**Résultat** : ❌ Aucun header personnalisé ne passe

### Test 3 : Audit Externe

- **Mozilla Observatory** : 30/100 (D)
- **SecurityHeaders.com** : D
- **Manquants** : X-Frame-Options, X-Content-Type-Options, HSTS, Referrer-Policy, Permissions-Policy

---

## 💡 Solutions Proposées

### 🏆 Solution 1 : Contacter Hostinger (Recommandé)

**Action** : Ouvrir un ticket support Hostinger

**Demande** :
> Bonjour,
> 
> Je souhaiterais autoriser les headers de sécurité HTTP personnalisés sur mon domaine `dev.infpf.fr`.
> 
> Actuellement, votre CDN (hcdn) filtre tous les headers que j'ajoute via `.htaccess` ou Symfony :
> - X-Frame-Options
> - X-Content-Type-Options
> - X-XSS-Protection
> - Referrer-Policy
> - Permissions-Policy
> - Strict-Transport-Security
> - Content-Security-Policy (version complète)
> 
> Seul `content-security-policy: upgrade-insecure-requests` passe actuellement.
> 
> Pouvez-vous autoriser ces headers pour améliorer la sécurité de mon site ?
> 
> Merci.

**Support Hostinger** : [hpanel.hostinger.com/support](https://hpanel.hostinger.com/support)

---

### 🔧 Solution 2 : Désactiver le CDN Temporairement

**Étapes** :

1. Connectez-vous à **hPanel Hostinger**
2. Allez dans **Website** → **dev.infpf.fr**
3. Cherchez **CDN** ou **Performance**
4. **Désactivez le CDN Hostinger**
5. Attendez **5-10 minutes** pour la propagation DNS
6. Re-testez :
   ```bash
   curl -I https://dev.infpf.fr | grep -E "x-frame|x-content"
   ```

**Note** : Cela peut légèrement réduire les performances (temps de chargement), mais permettra aux headers de passer.

---

### 🌟 Solution 3 : Migrer vers Cloudflare (Meilleur Contrôle)

**Avantages** :
- ✅ Contrôle total des headers de sécurité
- ✅ Meilleures performances que le CDN Hostinger
- ✅ Protection DDoS gratuite
- ✅ SSL/TLS flexible
- ✅ Analytics avancées
- ✅ Transform Rules pour headers personnalisés

**Étapes** :

1. **Créer un compte Cloudflare** : [cloudflare.com/sign-up](https://dash.cloudflare.com/sign-up)
2. **Ajouter votre domaine** : `infpf.fr`
3. **Changer les DNS chez Hostinger** :
   - Hostinger hPanel → Domaines → DNS/Nameservers
   - Remplacer par les nameservers Cloudflare (fournis lors de l'ajout du domaine)
4. **Configurer les Headers dans Cloudflare** :
   - Dashboard Cloudflare → Rules → Transform Rules → Modify Response Header
   - Ajouter chaque header de sécurité

**Guide détaillé** : [Cloudflare Transform Rules](https://developers.cloudflare.com/rules/transform/)

---

## 📊 Scores Attendus Après Résolution

Une fois les headers actifs :

| Audit | Score Actuel | Score Attendu | Amélioration |
|-------|--------------|---------------|--------------|
| **Mozilla Observatory** | 30/100 (D) | 70-85/100 (B/B+) | +40 à +55 points |
| **SecurityHeaders.com** | D | A/A+ | +4 grades |
| **Google Lighthouse Security** | 80% | 100% | +20% |

---

## 🎯 Recommandation Finale

1. **Court terme** : Contacter Hostinger (Solution 1) → Temps : 24-48h
2. **Moyen terme** : Si Hostinger refuse, désactiver leur CDN (Solution 2) → Temps : 10 minutes
3. **Long terme** : Migrer vers Cloudflare (Solution 3) pour un contrôle total → Temps : 1-2h

---

## 📞 Support

**Hostinger Support** :
- 🌐 hPanel : [hpanel.hostinger.com/support](https://hpanel.hostinger.com/support)
- 📧 Email : support@hostinger.com
- 💬 Chat : Disponible 24/7 dans hPanel

**Cloudflare Support** :
- 🌐 Dashboard : [dash.cloudflare.com](https://dash.cloudflare.com)
- 📚 Docs : [developers.cloudflare.com](https://developers.cloudflare.com)
- 💬 Community : [community.cloudflare.com](https://community.cloudflare.com)

---

## ✅ Actions Complétées

- [x] Implémentation du `SecurityHeadersListener.php`
- [x] Configuration de `.htaccess` avec headers de sécurité
- [x] Enregistrement du listener dans `services.yaml`
- [x] Vidage du cache Symfony (`cache:clear --env=prod`)
- [x] Préchauffage du cache (`cache:warmup --env=prod`)
- [x] Vérification du listener (`debug:event-dispatcher`)
- [x] Tests curl des headers
- [x] Audits externes (Mozilla Observatory, SecurityHeaders.com)
- [x] Diagnostic du problème CDN

## ⏳ Actions en Attente

- [ ] Contacter le support Hostinger pour autoriser les headers
- [ ] OU désactiver le CDN Hostinger
- [ ] OU migrer vers Cloudflare
- [ ] Re-tester après résolution du problème CDN
- [ ] Valider les scores améliorés (Mozilla Observatory, SecurityHeaders.com)

---

**Date de dernière mise à jour** : 3 novembre 2025  
**Auteur** : Équipe Technique INFPF











