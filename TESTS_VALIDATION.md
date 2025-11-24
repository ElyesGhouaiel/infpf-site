#  Tests de validation - Fonctionnalités implémentées

##  Objectif

Vérifier que **toutes les fonctionnalités** mises en place fonctionnent correctement.

---

##  CHECKLIST RAPIDE

###  1. PAGES D'ERREUR MODERNES

**Test :**
```bash
# Aller sur une page qui n'existe pas
https://dev.infpf.fr/page-inexistante-test
```

** Résultat attendu :**
- Page 404 moderne s'affiche
- Couleurs du site (#0b3f89)
- Formulaire de contact visible
- Boutons "Retour accueil" + "Page précédente" fonctionnels

**Test page 500 :**
- Provoquer une erreur volontaire (à faire en dev uniquement)

---

###  2. TESTS UNITAIRES & FONCTIONNELS

**Test local :**
```bash
cd /home/u665392393/domains/infpf.fr/dev
php bin/phpunit
```

** Résultat attendu :**
```
Tests: 42, Assertions: 62, Errors: 0, Failures: 0, Incomplete: 12, Skipped: 5
```

**Test CI/CD (GitHub Actions) :**
- Aller sur : https://github.com/ElyesGhouaiel/infpf-site/actions
- Vérifier que le workflow passe au vert 

---

###  3. SENTRY (MONITORING ERREURS)

**Test DSN configuré :**
```bash
cd /home/u665392393/domains/infpf.fr/dev
grep SENTRY_DSN .env
```

** Résultat attendu :**
```bash
SENTRY_DSN="https://b094d6d36a70d04dff26b577b8dc475f@o4510312920252416.ingest.de.sentry.io/4510312924512336"
```

**Test capture d'erreur :**
1. Aller sur : https://sentry.io/
2. Se connecter à votre compte
3. Vérifier le projet "INFPF"
4. Dashboard doit être accessible

**Test erreur volontaire (OPTIONNEL) :**
```bash
# Créer un fichier de test temporaire
echo '<?php throw new Exception("Test Sentry - DELETE ME");' > public/test-sentry.php

# Accéder à la page
https://dev.infpf.fr/test-sentry.php

# Aller sur Sentry.io → l'erreur doit apparaître

# Supprimer le fichier de test
rm public/test-sentry.php
```

---

###  4. LOGS STRUCTURÉS (MONOLOG)

**Test logs dev :**
```bash
cd /home/u665392393/domains/infpf.fr/dev
tail -f var/log/dev.log
```

** Résultat attendu :**
- Format JSON
- Logs en temps réel lors de la navigation

**Test logs prod :**
```bash
tail -f var/log/prod.log
```

---

###  5. RATE LIMITING (PROTECTION DDoS)

**Test formulaire contact :**

**Méthode 1 - Navigateur :**
1. Aller sur : https://dev.infpf.fr/contactez-nous
2. Soumettre le formulaire **6 fois rapidement**

** Résultat attendu :**
- Après 5 soumissions → Message d'erreur 429 "Trop de tentatives"
- Headers visibles dans DevTools :
  ```
  X-RateLimit-Limit: 5
  X-RateLimit-Remaining: 0
  X-RateLimit-Reset: [timestamp]
  ```

**Méthode 2 - cURL (plus rapide) :**
```bash
# Tester 6 requêtes POST rapidement
for i in {1..6}; do
  curl -X POST https://dev.infpf.fr/contactez-nous \
    -d "nom=Test&email=test@test.com&message=Test" \
    -i | grep -E "HTTP|X-RateLimit"
  echo "---"
done
```

** Résultat attendu :**
```
Requête 1-5 : HTTP/1.1 200 OK
Requête 6 : HTTP/1.1 429 Too Many Requests
X-RateLimit-Limit: 5
X-RateLimit-Remaining: 0
```

---

###  6. BACKUPS AUTOMATIQUES BDD

**Test manuel du script :**
```bash
cd /home/u665392393/domains/infpf.fr/dev
./bin/backup-database.sh
```

** Résultat attendu :**
```bash
[2025-11-05 XX:XX:XX] Début du backup de la base de données
[SUCCESS] Backup créé avec succès : /home/u665392393/backups/database/infpf_db_2025-11-05_XX-XX-XX.sql.gz (X.XM)
[2025-11-05 XX:XX:XX] Backup terminé avec succès
```

**Vérifier que le backup existe :**
```bash
ls -lh /home/u665392393/backups/database/
```

** Résultat attendu :**
```
-rw-r--r-- 1 user user 2.3M Nov  5 15:30 infpf_db_2025-11-05_15-30-00.sql.gz
```

**Vérifier les logs :**
```bash
cat /home/u665392393/backups/backup.log
```

**Test cron (OPTIONNEL) :**
```bash
# Vérifier que le cron est configuré
crontab -l | grep backup
```

** Si configuré, résultat attendu :**
```
0 3 * * * /home/u665392393/domains/infpf.fr/dev/bin/backup-database.sh >> /home/u665392393/backups/cron.log 2>&1
```

---

##  RÉSUMÉ DES TESTS

| Fonctionnalité | Test | Statut | Action |
|---------------|------|--------|--------|
| Pages erreur 404/500 | Accéder à `/page-test` |  À tester | Vérifier affichage |
| Tests unitaires | `php bin/phpunit` |  À tester | Vérifier pas d'erreurs |
| CI/CD GitHub | Actions workflow |  À tester | Voir badge vert |
| Sentry DSN | `grep SENTRY_DSN .env` |  Configuré | Vérifier dashboard |
| Logs JSON | `tail var/log/dev.log` |  Configuré | Vérifier format |
| Rate Limiting | 6 POST rapides |  À tester | Vérifier erreur 429 |
| Backup BDD | `./bin/backup-database.sh` |  À tester | Vérifier fichier .gz |

---

##  EN CAS DE PROBLÈME

###  Pages d'erreur ne s'affichent pas
```bash
cd /home/u665392393/domains/infpf.fr/dev
php bin/console cache:clear
```

###  Sentry ne capture pas les erreurs
```bash
# Vérifier que le bundle est activé
grep SentryBundle config/bundles.php
```

###  Rate limiting ne fonctionne pas
```bash
# Vérifier la config
cat config/packages/rate_limiter.yaml
php bin/console debug:container limiter.contact_form
```

###  Backup échoue
```bash
# Vérifier les permissions
ls -l bin/backup-database.sh
# Doit être : -rwxr-xr-x (exécutable)

# Vérifier DATABASE_URL
grep DATABASE_URL .env

# Tester mysqldump manuellement
mysqldump -u root infpf_db | head -20
```

---

##  TESTS PRIORITAIRES (5 MINUTES)

**Si tu veux tester rapidement, fais UNIQUEMENT ceux-ci :**

1. **Page 404** : `https://dev.infpf.fr/test-404` → Page moderne s'affiche ? 
2. **Sentry Dashboard** : https://sentry.io/ → Projet visible ? 
3. **Backup manuel** : `./bin/backup-database.sh` → Fichier .gz créé ? 
4. **Rate Limiting** : 6 soumissions formulaire → Erreur 429 ? 

**Si les 4 fonctionnent → TOUT EST OK ! **

---

## 📞 Support

**Problème ?**
- Email : elyes@xeilos.fr
- Logs : `var/log/dev.log` et `/home/u665392393/backups/backup.log`

---

**Date de création** : 2025-11-05  
**Dernière mise à jour** : 2025-11-05
