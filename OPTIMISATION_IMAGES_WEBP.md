# 🖼️ Optimisation des Images WebP - INFPF

Ce document explique comment optimiser automatiquement toutes les images du site en **WebP** pour des performances maximales.

---

## 📊 Pourquoi WebP ?

**WebP** est un format d'image moderne développé par Google qui offre :

- ✅ **25-35% plus léger** que JPEG à qualité équivalente
- ✅ **26% plus léger** que PNG pour les images avec transparence
- ✅ **Support natif** dans 97% des navigateurs modernes (Chrome, Firefox, Edge, Safari 14+)
- ✅ **Pas de perte de qualité** visible à l'œil nu
- ✅ **Compatible avec le lazy loading** déjà en place

### Gains attendus sur INFPF

| Type d'image | Format actuel | Taille moyenne | Format WebP | Taille moyenne | Gain |
|--------------|---------------|----------------|-------------|----------------|------|
| Logos/Icônes | PNG | 50 KB | WebP | 15 KB | **-70%** |
| Photos/Bannières | JPEG | 200 KB | WebP | 140 KB | **-30%** |
| Images blog | JPEG | 150 KB | WebP | 105 KB | **-30%** |

**Économie totale estimée** : **2-3 MB** sur le poids total du site → **Chargement 20-30% plus rapide**.

---

## 🚀 Utilisation du Script d'Optimisation

### 1. Installation de cwebp (sur Hostinger)

**Via SSH** (si disponible) :

```bash
# CentOS/RHEL (Hostinger utilise généralement CentOS)
sudo yum install libwebp-tools

# Ou Ubuntu/Debian
sudo apt install webp
```

**Si tu n'as pas accès SSH**, contacte le support Hostinger et demande :
> "Pouvez-vous installer le paquet `libwebp-tools` sur mon hébergement pour optimiser les images en WebP ?"

### 2. Vérification de l'installation

```bash
cwebp -version
# Devrait afficher : version libwebp-X.X.X
```

### 3. Exécution du Script

**a) Convertir uniquement les nouvelles images** (par défaut) :

```bash
cd /home/u665392393/domains/infpf.fr/dev
./bin/optimize-images-webp.sh --new
```

**b) Reconvertir toutes les images** (même celles déjà converties) :

```bash
./bin/optimize-images-webp.sh --all
```

### 4. Exemple de Sortie

```
================================================
🚀 Optimisation des images en WebP
================================================

Mode sélectionné : --new
Qualité WebP     : 85

📂 Traitement : /home/u665392393/domains/infpf.fr/dev/public/img

✅ Converti : AdministratifsImage.webp (gain: 0.05 MB)
✅ Converti : AutresImage.webp (gain: 0.03 MB)
⏭️  Déjà converti : DesignImage.webp
✅ Converti : IAImage.webp (gain: 0.08 MB)
...

📂 Traitement : /home/u665392393/domains/infpf.fr/dev/public/uploads/images

✅ Converti : cybersecurite.webp (gain: 0.12 MB)
✅ Converti : finance-2.webp (gain: 0.09 MB)
...

================================================
✅ Optimisation terminée !
================================================

📊 Images converties : 87
⏭️  Images ignorées   : 12
💾 Espace économisé  : 2.45 MB

💡 Prochaines étapes :
  1. Vérifie que les .webp sont bien générés
  2. Active le mod_rewrite dans .htaccess (déjà fait)
  3. Teste avec : curl -H "Accept: image/webp" -I https://dev.infpf.fr/img/logo.png
```

---

## 🔧 Configuration Automatique (.htaccess)

Le `.htaccess` a été modifié pour **servir automatiquement WebP** si :
1. Le navigateur supporte WebP (`Accept: image/webp`)
2. Une version WebP de l'image existe (ex: `logo.webp` pour `logo.png`)

### Comment ça marche ?

```apache
# Si le navigateur supporte WebP
RewriteCond %{HTTP_ACCEPT} image/webp

# Et si l'image WebP existe
RewriteCond %{REQUEST_FILENAME} (.*)\.(jpe?g|png)$
RewriteCond %1.webp -f

# Servir le WebP à la place
RewriteRule ^(.+)\.(jpe?g|png)$ $1.webp [T=image/webp,E=REQUEST_image,L]
```

**Avantages** :
- ✅ Transparent pour le code HTML (pas besoin de changer `<img src="logo.png">`)
- ✅ Fallback automatique sur JPEG/PNG si le navigateur ne supporte pas WebP
- ✅ Pas besoin de balise `<picture>` complexe

---

## ✅ Vérification du Fonctionnement

### 1. Vérifier que les .webp sont générés

```bash
ls -lh /home/u665392393/domains/infpf.fr/dev/public/img/ | grep webp
ls -lh /home/u665392393/domains/infpf.fr/dev/public/uploads/images/ | grep webp
```

Tu devrais voir plein de fichiers `.webp` avec des tailles réduites.

### 2. Tester le serveur WebP

**a) Via curl** :

```bash
# Teste une image (remplace par une vraie image)
curl -H "Accept: image/webp" -I https://dev.infpf.fr/img/LOGO__INFPF.png
```

**Résultat attendu** :
```
HTTP/2 200
content-type: image/webp   <-- ✅ Bien servi en WebP
...
```

**b) Via navigateur** :

1. Ouvre **DevTools** (F12) dans Chrome/Firefox
2. Va sur l'onglet **Network** > Filtre **Img**
3. Recharge la page
4. Clique sur une image → Regarde le header **Content-Type**
   - Doit afficher : `Content-Type: image/webp` ✅
   - Ou : `Content-Type: image/jpeg` (si WebP non généré)

### 3. Tester avec Google PageSpeed Insights

Va sur : https://pagespeed.web.dev/

Teste `https://dev.infpf.fr`

**Avant WebP** :
- "Serve images in next-gen formats" → ⚠️ Avertissement

**Après WebP** :
- "Serve images in next-gen formats" → ✅ Validé
- **Score Lighthouse** : +2-5 points

---

## 📈 Automatisation (Cron Job)

Pour convertir automatiquement les nouvelles images uploadées :

### 1. Créer un Cron Job via Hostinger hPanel

1. Connexion **hPanel** > **Cron Jobs**
2. Ajoute une nouvelle tâche :
   - **Commande** : `/home/u665392393/domains/infpf.fr/dev/bin/optimize-images-webp.sh --new`
   - **Fréquence** : Quotidien à 03h00 (ou hebdomadaire)
3. Sauvegarde

### 2. Ou via crontab (SSH)

```bash
crontab -e

# Ajoute cette ligne (exécution tous les jours à 3h du matin)
0 3 * * * /home/u665392393/domains/infpf.fr/dev/bin/optimize-images-webp.sh --new >> /home/u665392393/domains/infpf.fr/dev/var/log/webp-conversion.log 2>&1
```

---

## 🖼️ Modifier le Code pour Utiliser WebP (Optionnel)

Si tu veux **forcer** l'utilisation de WebP dans le HTML (au lieu de `.htaccess` automatique), tu peux utiliser la balise `<picture>` :

### Exemple Avant :

```html
<img src="/img/logo.png" alt="Logo INFPF">
```

### Exemple Après :

```html
<picture>
    <source srcset="/img/logo.webp" type="image/webp">
    <img src="/img/logo.png" alt="Logo INFPF" loading="lazy">
</picture>
```

**Mais ce n'est PAS nécessaire** car `.htaccess` le fait déjà automatiquement ! 😉

---

## 🔍 Dépannage

### Problème 1 : `cwebp: command not found`

**Solution** : Installe `cwebp` (voir section Installation)

### Problème 2 : Les .webp ne sont pas servis

**Vérifications** :
1. Le fichier `.webp` existe bien dans le même répertoire que le `.png`/`.jpg`
2. `mod_rewrite` est activé sur Hostinger (normalement oui)
3. Les permissions du fichier `.webp` sont correctes (`chmod 644 *.webp`)

**Test manuel** :

```bash
# Teste directement le .webp
curl -I https://dev.infpf.fr/img/logo.webp

# Devrait retourner : Content-Type: image/webp
```

### Problème 3 : Images floues ou de mauvaise qualité

**Solution** : Augmente la qualité dans le script

```bash
# Édite bin/optimize-images-webp.sh
QUALITY=90  # Au lieu de 85 (défaut)
```

Puis reconvertis avec `--all` :

```bash
./bin/optimize-images-webp.sh --all
```

---

## 📊 Monitoring des Performances

Après avoir activé WebP, surveille les métriques :

### 1. Google PageSpeed Insights

- **Avant** : Note Mobile / Desktop
- **Après** : Note Mobile / Desktop
- **Gain attendu** : +2-5 points

### 2. Lighthouse (DevTools)

- **Performance** : Devrait augmenter de 2-5 points
- **Best Practices** : "Serve images in next-gen formats" doit être ✅

### 3. GTmetrix / Pingdom

- **Page Load Time** : Devrait diminuer de 15-25%
- **Total Page Size** : Devrait diminuer de 20-30%

---

## 🎯 Checklist de Déploiement Production

Avant de déployer sur `infpf.fr` (production) :

- [ ] ✅ Teste sur `dev.infpf.fr` pendant 1-2 jours
- [ ] ✅ Vérifie que toutes les images s'affichent correctement
- [ ] ✅ Teste sur plusieurs navigateurs (Chrome, Firefox, Safari, Edge)
- [ ] ✅ Vérifie Lighthouse : Score doit être ≥ 98
- [ ] ✅ Copie le script sur production :
  ```bash
  cp /home/u665392393/domains/infpf.fr/dev/bin/optimize-images-webp.sh \
     /home/u665392393/domains/infpf.fr/public_html/bin/
  ```
- [ ] ✅ Exécute le script en production :
  ```bash
  cd /home/u665392393/domains/infpf.fr/public_html
  ./bin/optimize-images-webp.sh --all
  ```
- [ ] ✅ Active le Cron Job en production (via hPanel)

---

## 📚 Ressources

- [Documentation WebP Google](https://developers.google.com/speed/webp)
- [Can I Use WebP](https://caniuse.com/webp) - Support navigateurs : 97.8%
- [Guide Hostinger - Optimisation Images](https://support.hostinger.com/)

---

## 🆘 Support

Si tu rencontres un problème :

1. Vérifie les logs : `var/log/webp-conversion.log`
2. Teste manuellement : `cwebp -q 85 input.jpg -o output.webp`
3. Contacte le support Hostinger si `cwebp` ne fonctionne pas

---

**Dernière mise à jour** : 06/11/2025  
**Auteur** : Optimisations Claude Sonnet 4.5 pour INFPF




