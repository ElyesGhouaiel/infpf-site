# 🚀 Optimisations Performance - Hostinger

Ce document détaille toutes les optimisations mises en place pour maximiser les performances sur **Hostinger** (sans CDN externe).

---

## ✅ Optimisations Déjà en Place

### 1. Cache HTTP Navigateur (`.htaccess`)
- **Images** : cache 1 an (`max-age=31536000`)
- **CSS/JS** : cache 1 an avec `immutable`
- **Fonts** : cache 1 an
- **HTML/PHP** : pas de cache (dynamique)

### 2. Compression Gzip/Brotli
- Compression niveau 9 (maximum) pour tous les fichiers texte
- Support Brotli si disponible sur Hostinger
- Headers `Vary: Accept-Encoding` pour les proxies

### 3. Headers de Sécurité
- ✅ `X-XSS-Protection`
- ✅ `X-Content-Type-Options: nosniff`
- ✅ `X-Frame-Options: SAMEORIGIN`
- ✅ `Referrer-Policy: strict-origin-when-cross-origin`
- ✅ `Content-Security-Policy` (compatible Google/Calendly/Stripe)
- ✅ `Permissions-Policy`

---

## 🆕 Nouvelles Optimisations (JOUR 5)

### 1. Cache Symfony avec OPcache PHP

**Fichier** : `config/packages/prod/cache.yaml`

Symfony utilise maintenant `cache.adapter.php_files` qui tire parti d'**OPcache** (disponible sur Hostinger) :

- ✅ **Cache app** : fichiers PHP compilés par OPcache
- ✅ **Doctrine metadata** : cache 1h
- ✅ **Doctrine queries** : cache 1h
- ✅ **Traductions** : cache 24h
- ✅ **Serializer** : cache 24h
- ✅ **Validator** : cache 24h
- ✅ **Annotations** : cache 24h

**Gains attendus** :
- ⚡ **30-50% plus rapide** sur les requêtes Doctrine
- ⚡ **20-30% moins de mémoire** consommée
- ⚡ **Réduction de la charge CPU**

---

### 2. Vérification OPcache

**Script de test** : `/public/opcache-check.php`

Accède à ce script via : **https://dev.infpf.fr/opcache-check.php**

Le script affiche :
- ✅ Statut OPcache (activé/désactivé)
- 📊 Statistiques (hit rate, mémoire utilisée, scripts cachés)
- ⚙️ Configuration (memory_consumption, max_accelerated_files)
- 💡 Recommandations automatiques

**Important** : ⚠️ **Supprime ce fichier en production** (pour sécurité) :

```bash
rm /home/u665392393/domains/infpf.fr/public_html/opcache-check.php
```

---

### 3. Optimisation Doctrine

**Fichier** : `config/packages/prod/doctrine.yaml`

- ✅ **Désactive la génération automatique de proxies** (`auto_generate_proxy_classes: false`)
- ✅ **Cache metadata** : fichiers PHP (OPcache)
- ✅ **Cache queries** : fichiers PHP (OPcache)
- ✅ **Cache results** : fichiers PHP (OPcache)

**Gains attendus** :
- ⚡ **40-60% plus rapide** sur les requêtes répétitives
- ⚡ **Moins de requêtes SQL** grâce au cache de résultats

---

## 📊 Comment Tester les Performances

### 1. Vérifier OPcache

```bash
# Via le script de test
curl https://dev.infpf.fr/opcache-check.php | jq

# Ou directement dans phpinfo() via le profiler Symfony
https://dev.infpf.fr/_profiler/phpinfo
```

### 2. Tester la Vitesse Avant/Après

**Avant les optimisations** :
```bash
curl -w "@curl-format.txt" -o /dev/null -s https://dev.infpf.fr/
```

**Après les optimisations** :
```bash
# Vide le cache Symfony
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod

# Teste à nouveau
curl -w "@curl-format.txt" -o /dev/null -s https://dev.infpf.fr/
```

### 3. Lighthouse / PageSpeed Insights

- Avant : **Mobile 97 | Desktop 99**
- Objectif après : **Mobile 98-100 | Desktop 100**

---

## 🎯 Recommandations Hostinger

### 1. Activer OPcache (si pas déjà fait)

**Via le panneau Hostinger** :
1. Connexion à **hPanel**
2. Va dans **Paramètres PHP**
3. Vérifie que **OPcache** est coché/activé
4. Redémarre PHP-FPM si nécessaire

**Paramètres OPcache recommandés** :
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.validate_timestamps=1
opcache.save_comments=1
```

### 2. Activer la Compression Gzip/Brotli

Normalement déjà activé sur Hostinger, mais tu peux vérifier :

```bash
# Teste si Gzip est actif
curl -H "Accept-Encoding: gzip" -I https://dev.infpf.fr/ | grep -i "content-encoding"

# Résultat attendu : content-encoding: gzip
```

### 3. Vérifier les Limites PHP

**Via hPanel > Paramètres PHP** :
- `memory_limit` : **256M minimum** (idéal : 512M)
- `max_execution_time` : **60s minimum**
- `upload_max_filesize` : **64M minimum** (pour les formations)
- `post_max_size` : **64M minimum**

---

## 🚀 Déploiement en Production

Une fois testé sur `dev.infpf.fr`, pour déployer sur `infpf.fr` :

### 1. Merge la branche de dev

```bash
cd /home/u665392393/domains/infpf.fr/dev
git checkout main
git merge feature/performance-security-seo-optimization
git push origin main
```

### 2. Déploie sur production

```bash
cd /home/u665392393/domains/infpf.fr/public_html

# Pull les changements
git pull origin main

# Installe les dépendances (prod only)
composer install --no-dev --optimize-autoloader

# Vide et réchauffe le cache
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod

# Optimise l'autoloader
composer dump-autoload --optimize --classmap-authoritative
```

### 3. Vérifie OPcache en prod

```bash
# Accède temporairement au script (puis supprime-le)
curl https://www.infpf.fr/opcache-check.php | jq

# ⚠️ Supprime-le immédiatement après
rm /home/u665392393/domains/infpf.fr/public_html/public/opcache-check.php
```

---

## 📈 Gains de Performance Attendus

| Métrique | Avant | Après | Gain |
|----------|-------|-------|------|
| **TTFB** (Time To First Byte) | ~200ms | ~100-120ms | **40-50%** |
| **Page Load** (DOM Ready) | ~1.2s | ~0.8-1s | **20-30%** |
| **Lighthouse Mobile** | 97 | 98-100 | **+1-3 points** |
| **Lighthouse Desktop** | 99 | 100 | **+1 point** |
| **Doctrine Queries** | Variable | 40-60% plus rapides | **40-60%** |
| **Memory Usage** | Variable | 20-30% moins | **20-30%** |

---

## ❓ FAQ

### Q1 : OPcache ralentit-il le développement ?
**R** : Non, car `config/packages/prod/cache.yaml` ne s'applique qu'en **environnement prod**. En dev, le cache classique est utilisé.

### Q2 : Faut-il vider le cache OPcache après chaque déploiement ?
**R** : OPcache se met à jour automatiquement après 60 secondes (`opcache.revalidate_freq=60`). Pour un redémarrage immédiat :

```bash
# Via Hostinger hPanel : redémarre PHP-FPM
# Ou via CLI si disponible :
systemctl restart php-fpm
```

### Q3 : Pourquoi ne pas utiliser Redis/Memcached ?
**R** : Hostinger ne fournit généralement **pas** Redis/Memcached sur les plans partagés. OPcache est la meilleure alternative (et très performant pour un site Symfony).

### Q4 : Les images sont-elles optimisées ?
**R** : Oui, lazy loading déjà en place (JOUR 1). Pour aller plus loin, voir **JOUR 5 - Compression images WebP**.

---

## 🔧 Maintenance

### Vider le cache Symfony (prod)

```bash
cd /home/u665392393/domains/infpf.fr/public_html
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

### Vider le cache OPcache

```bash
# Via Hostinger hPanel : Outils > Gestionnaire PHP > Redémarrer PHP-FPM
```

### Monitorer les performances

- **Sentry** : https://sentry.io (erreurs + performance)
- **UptimeRobot** : https://uptimerobot.com (uptime)
- **Google Analytics** : https://analytics.google.com (trafic)

---

## 📝 Prochaines Étapes

- [ ] **JOUR 5** : Compression images WebP automatique
- [ ] **JOUR 6** : Documentation complète
- [ ] **JOUR 7** : Audit sécurité OWASP

---

**Dernière mise à jour** : 06/11/2025  
**Auteur** : Optimisations Claude Sonnet 4.5 pour INFPF

