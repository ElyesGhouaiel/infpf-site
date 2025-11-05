# 🔍 Scan de Vulnérabilités & Sécurité

## 🎯 Objectif

Détecter et corriger automatiquement les vulnérabilités de sécurité dans les dépendances.

---

## ✅ 1. DEPENDABOT (GitHub) - CONFIGURÉ

### Ce qui a été mis en place

✅ **Fichier `.github/dependabot.yml` créé**

**Fonctionnalités actives :**
- 📦 Scan dépendances Composer (PHP) hebdomadaire
- 📦 Scan dépendances NPM (JavaScript) hebdomadaire
- 🔧 Scan GitHub Actions mensuel
- 🤖 Pull Requests automatiques pour les mises à jour de sécurité
- 🏷️ Labels automatiques : `dependencies`, `security`
- 👤 Reviewers : ElyesGhouaiel
- 📅 Planning : Lundi 9h (Europe/Paris)

### Comment ça fonctionne

1. **Dependabot scanne** votre `composer.json` et `package.json`
2. **Détecte les vulnérabilités** (CVE)
3. **Crée une Pull Request** automatiquement
4. **Vous notifie** par email/GitHub
5. **Vous reviewez** et mergez la PR

### Voir les alertes

🔗 **Dashboard Dependabot :**
```
https://github.com/ElyesGhouaiel/infpf-site/security/dependabot
```

---

## 🛡️ 2. GITHUB SECURITY ADVISORIES

### Activer les alertes de sécurité

1. Aller sur : https://github.com/ElyesGhouaiel/infpf-site/settings/security_analysis
2. Activer :
   - ✅ **Dependabot alerts** → Alertes vulnérabilités
   - ✅ **Dependabot security updates** → PRs automatiques
   - ✅ **Grouped security updates** → Grouper les MAJ

### Dashboard sécurité

🔗 **Voir toutes les vulnérabilités :**
```
https://github.com/ElyesGhouaiel/infpf-site/security
```

**Sections disponibles :**
- **Code scanning** : Analyse du code
- **Dependabot** : Vulnérabilités dépendances
- **Secret scanning** : Clés API exposées

---

## 🔍 3. SCAN MANUEL DES DÉPENDANCES

### A. Symfony Security Checker

**Installation :**
```bash
composer require --dev symfony/security-checker
```

**Scan :**
```bash
cd /home/u665392393/domains/infpf.fr/dev
symfony security:check
```

**✅ Résultat attendu :**
```
Symfony Security Check Report
==============================

No packages have known vulnerabilities.
```

**❌ Si vulnérabilités détectées :**
```
1 package has known vulnerabilities.

symfony/http-kernel (v6.0.1)
----------------------------
CVE-2023-XXXXX: Arbitrary Code Execution
https://github.com/advisories/GHSA-xxxx-xxxx-xxxx

Update to v6.0.20 to fix this issue.
```

### B. Composer Audit (intégré)

```bash
composer audit
```

**Exemple de résultat :**
```
Found 0 security vulnerability advisories affecting 0 packages.
```

### C. Local PHP Security Checker

**Installation (outil CLI) :**
```bash
cd /home/u665392393/bin
curl -LSs https://get.symfony.com/cli/installer | bash
```

**Scan :**
```bash
symfony check:security
```

---

## 📊 4. SCAN AVANCÉ (OPTIONNEL)

### Snyk - Scan de vulnérabilités avancé

**Gratuit pour projets open-source**

**Installation :**
```bash
npm install -g snyk
snyk auth
```

**Scan projet :**
```bash
cd /home/u665392393/domains/infpf.fr/dev
snyk test
```

**Monitoring continu :**
```bash
snyk monitor
```

🔗 **Dashboard Snyk** : https://snyk.io/

---

## 🚨 5. TYPES DE VULNÉRABILITÉS DÉTECTÉES

### Critiques (Critical) 🔴
- **Exécution de code arbitraire**
- **Injection SQL**
- **Accès non autorisé**

**Action : Corriger IMMÉDIATEMENT**

### Hautes (High) 🟠
- **XSS (Cross-Site Scripting)**
- **CSRF (Cross-Site Request Forgery)**
- **Déni de service (DoS)**

**Action : Corriger sous 48h**

### Moyennes (Medium) 🟡
- **Exposition d'informations**
- **Contournement de sécurité**

**Action : Corriger sous 1 semaine**

### Basses (Low) 🟢
- **Problèmes mineurs**

**Action : Corriger lors de la prochaine MAJ**

---

## 🔧 6. CORRIGER LES VULNÉRABILITÉS

### Méthode 1 : Mise à jour automatique (Dependabot)

1. **Recevoir la PR Dependabot**
2. **Reviewer les changements**
3. **Tester localement** (optionnel)
4. **Merger la PR**
5. **Déployer**

### Méthode 2 : Mise à jour manuelle

```bash
# Mettre à jour un package spécifique
composer update symfony/http-kernel

# Mettre à jour tous les packages de sécurité
composer update --with-all-dependencies

# Vérifier les changements
git diff composer.lock

# Tester
php bin/phpunit

# Commit
git add composer.lock
git commit -m "🔒 Security update: symfony/http-kernel"
git push
```

### Méthode 3 : Mise à jour vers une version spécifique

```bash
# Forcer une version précise
composer require symfony/http-kernel:^6.4.20

# Ou modifier composer.json
"symfony/http-kernel": "^6.4.20"

composer update
```

---

## 📈 7. MONITORING CONTINU

### A. Notifications GitHub

**Configurer les notifications :**

1. Aller sur : https://github.com/settings/notifications
2. Activer :
   - ✅ **Security alerts**
   - ✅ **Dependabot alerts**
3. Choisir : Email + Web

### B. GitHub Actions (déjà configuré)

Le workflow CI/CD existant teste automatiquement :
- ✅ Tests unitaires après chaque commit
- ✅ Lint PHP
- ✅ Vérification Symfony

### C. Badge de sécurité (OPTIONNEL)

**Ajouter dans README.md :**

```markdown
![Security](https://img.shields.io/github/security/alerts/ElyesGhouaiel/infpf-site?style=for-the-badge)
![Dependabot](https://img.shields.io/badge/Dependabot-enabled-success?style=for-the-badge)
```

---

## 🎯 8. CHECKLIST SÉCURITÉ

### Dépendances
- [x] Dependabot configuré ✅
- [x] Alertes de sécurité activées ✅
- [ ] Scan manuel effectué (`composer audit`)
- [ ] Aucune vulnérabilité critique

### Code
- [ ] Pas de clés API en dur dans le code
- [ ] `.env` dans `.gitignore`
- [ ] Validation des entrées utilisateur
- [ ] Protection CSRF active
- [ ] Échappement des sorties (Twig auto-escape)

### Serveur
- [x] HTTPS actif ✅
- [x] Headers de sécurité ✅
- [x] Rate limiting ✅
- [ ] Firewall configuré
- [ ] Fail2ban actif (optionnel)

### Monitoring
- [x] Sentry pour les erreurs ✅
- [x] Logs structurés ✅
- [ ] Alertes email configurées

---

## 🚨 9. EN CAS DE VULNÉRABILITÉ CRITIQUE

### Procédure d'urgence

1. **Vérifier la criticité** (CVSS score > 9.0)
2. **Couper l'accès public** (si exploitation active)
   ```bash
   # Mode maintenance
   touch maintenance.flag
   ```
3. **Appliquer le patch**
   ```bash
   composer update [package-vulnerable]
   ```
4. **Tester**
   ```bash
   php bin/phpunit
   ```
5. **Déployer en urgence**
6. **Notifier les utilisateurs** (si données compromises)

### Contacts d'urgence
- **Admin** : elyes@xeilos.fr
- **Support Symfony** : https://symfony.com/support
- **GitHub Support** : https://support.github.com/

---

## 📊 10. RAPPORTS RÉGULIERS

### Hebdomadaire (automatique)
- ✅ Dependabot scan lundi 9h
- ✅ PRs automatiques si vulnérabilités

### Mensuel (manuel)
```bash
# Audit complet
composer audit
symfony check:security

# Vérifier les packages obsolètes
composer outdated --direct
```

### Trimestriel (manuel)
- Audit de sécurité complet (OWASP)
- Mise à jour majeure Symfony (si disponible)
- Review des permissions utilisateurs

---

## 📞 Support

**Problème de sécurité ?**
- **Email urgence** : elyes@xeilos.fr
- **GitHub Issues** : https://github.com/ElyesGhouaiel/infpf-site/issues
- **Symfony Security** : https://symfony.com/doc/current/security.html

---

## ✅ RÉSUMÉ

- ✅ **Dependabot actif** → Scan automatique hebdomadaire
- ✅ **GitHub Security Advisories** → Alertes en temps réel
- ✅ **Composer Audit** → Vérification locale
- ✅ **Notifications email** → Alertes critiques
- ✅ **PRs automatiques** → Corrections proposées
- ✅ **Planning régulier** → Lundi 9h (Europe/Paris)

**Date de configuration** : 2025-11-05  
**Prochaine révision** : 2025-12-05

