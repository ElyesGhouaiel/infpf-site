# ⚡ TESTS RAPIDES - Ce Qui a Été Fait

**Durée** : 5 minutes pour tout vérifier rapidement

---

## 🎯 9 Fonctionnalités à Tester

| # | Fonctionnalité | Test Rapide | Temps | ✅ |
|---|----------------|-------------|-------|---|
| 1 | **Pages d'erreur 404/500/403** | Visitez `/test/error/404` `/test/error/500` `/test/error/403` | 1 min | ☐ |
| 2 | **Formulaires email direct** | Remplissez un formulaire sur page d'erreur | 2 min | ☐ |
| 3 | **Sentry (monitoring)** | Visitez `/test-sentry.php` → Vérifiez sentry.io | 1 min | ☐ |
| 4 | **Rate Limiting** | 6 requêtes curl → 6ème = HTTP 429 | 1 min | ☐ |
| 5 | **Backup BDD** | `./bin/backup-database.sh` → Fichier créé | 30s | ☐ |
| 6 | **Headers HTTPS** | `curl -I /test-headers.php` → HSTS présent | 30s | ☐ |
| 7 | **Scan vulnérabilités** | GitHub Dependabot actif | 30s | ☐ |
| 8 | **Tests PHPUnit** | `php bin/phpunit` → OK | 1 min | ☐ |
| 9 | **Logs JSON** | `tail var/log/prod.log` → Format JSON | 30s | ☐ |

---

## 🧪 URLs de Test Rapide

```
✅ Pages d'erreur :
https://dev.infpf.fr/test/error/404
https://dev.infpf.fr/test/error/500
https://dev.infpf.fr/test/error/403

✅ Test Sentry :
https://dev.infpf.fr/test-sentry.php

✅ Dashboard Sentry :
https://sentry.io/organizations/infpf/projects/php-symfony/

✅ Headers HTTPS :
https://dev.infpf.fr/test-headers.php

✅ GitHub Dependabot :
https://github.com/ElyesGhouaiel/infpf-site/security/dependabot
```

---

## ⚡ Test Ultra-Rapide (1 min)

```bash
# Dans votre terminal SSH
cd /home/u665392393/domains/infpf.fr/dev

# 1. Backup BDD (5 sec)
./bin/backup-database.sh

# 2. Rate Limiting (10 sec)
for i in {1..6}; do curl -X POST https://dev.infpf.fr/contactez-nous -d "nom=Test" -s -o /dev/null -w "Req $i: %{http_code}\n"; done

# 3. Headers HTTPS (2 sec)
curl -I https://dev.infpf.fr/test-headers.php | grep -i strict-transport

# 4. Logs JSON (2 sec)
tail -2 var/log/prod.log

# 5. Tests PHPUnit (30 sec)
php bin/phpunit --testdox
```

**Si tout est OK** → ✅ Tout fonctionne !

---

## 📧 Vérifier Email Formulaires

1. Allez sur : `https://dev.infpf.fr/test/error/404`
2. Remplissez le formulaire
3. Cliquez "Envoyer"
4. **Vérifiez `elyes@xeilos.fr`** dans les 30 secondes

**Attendu** :
- ✅ Email reçu avec sujet "🔴 Erreur 404 signalée sur INFPF"
- ✅ Contenu HTML formaté
- ✅ Reply-To = email du visiteur

---

## 🎯 Checklist Visuelle

```
┌─────────────────────────────────────────┐
│  JOUR 1 & 2 : 9 FONCTIONNALITÉS        │
├─────────────────────────────────────────┤
│  ☐ Pages d'erreur modernes              │
│  ☐ Formulaires email direct              │
│  ☐ Sentry monitoring                     │
│  ☐ Rate Limiting                         │
│  ☐ Backup BDD automatique                │
│  ☐ Headers HTTPS (HSTS)                  │
│  ☐ Scan vulnérabilités                   │
│  ☐ Tests PHPUnit OK                      │
│  ☐ Logs JSON structurés                  │
└─────────────────────────────────────────┘

✅ TOUT COCHÉ = PRÊT POUR JOUR 3 !
```

---

## 🚀 Si Tout Fonctionne

**JOUR 1 + 2 = 100% TERMINÉS !** 🎉

**Prochaine étape** : JOUR 3
- UptimeRobot (surveillance 24/7)
- Google Analytics (statistiques visiteurs)

**Temps estimé JOUR 3** : 2-3 heures

---

**Pour plus de détails** : Voir `GUIDE_VALIDATION_COMPLET.md`

