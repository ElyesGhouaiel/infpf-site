#  GUIDE DE VALIDATION COMPLET - JOUR 1 & 2

**Date** : 5 novembre 2025, 17h45  
**Durée totale** : ~20 minutes  
**Objectif** : Vérifier que TOUT fonctionne !

---

##  RÉCAPITULATIF : Ce Qui A Été Fait

###  JOUR 1 (4 fonctionnalités)
1. **Pages d'erreur modernes** (404, 500, 403)
2. **Tests PHPUnit corrigés** (100% passent)
3. **Sentry** (monitoring erreurs en temps réel)
4. **Logs structurés** (Monolog JSON)

###  JOUR 2 (4 fonctionnalités)
5. **Rate Limiting** (protection DDoS/spam)
6. **Backups BDD automatiques** (quotidiens)
7. **SSL/HTTPS renforcé** (HSTS)
8. **Scan vulnérabilités** (Dependabot)

###  BONUS
9. **Formulaires pages d'erreur** (envoi email direct)

**TOTAL : 9 fonctionnalités majeures** 

---

## 🧪 VALIDATION COMPLÈTE (20 min)

###  TEST 1 : Pages d'Erreur (3 min)

**Objectif** : Vérifier que les pages d'erreur sont modernes et fonctionnelles

#### 1.1 Page 404
```
👉 https://dev.infpf.fr/test/error/404
```
**Attendu** :
-  Design moderne aux couleurs du site
-  Message clair : "Page non trouvée"
-  Boutons "Retour à l'accueil" + "Page précédente"
-  Formulaire de contact présent
-  Pas de page Symfony de debug !

#### 1.2 Page 500
```
👉 https://dev.infpf.fr/test/error/500
```
**Attendu** :
-  Design moderne rouge (erreur serveur)
-  Message : "Une erreur interne s'est produite"
-  Bouton "Actualiser" + "Retour à l'accueil"
-  Formulaire "Signaler cette erreur"

#### 1.3 Page 403
```
👉 https://dev.infpf.fr/test/error/403
```
**Attendu** :
-  Design moderne orange/or (accès interdit)
-  Message : "Accès refusé"
-  Formulaire de demande d'accès

** VALIDATION** : Cochez si les 3 pages s'affichent correctement

---

###  TEST 2 : Formulaires Pages d'Erreur (5 min)

**Objectif** : Vérifier l'envoi d'email direct (sans mailto:)

#### 2.1 Test Page 404
```
👉 https://dev.infpf.fr/test/error/404
```
1. Remplissez le formulaire "💬 Besoin d'aide ?"
   - Nom : Votre nom
   - Email : Votre email
   - Message : "Test validation JOUR 2"
2. Cliquez "Envoyer le message"
3. **Attendu** :
   -  Bouton affiche "Envoi en cours..."
   -  Après 1-2 sec : Message vert " Votre message a été envoyé avec succès !"
   -  Formulaire se vide automatiquement
   -  **PAS de popup "not secure" !**

#### 2.2 Vérifier Email Reçu
```
👉 Ouvrez votre boîte elyes@xeilos.fr
```
**Attendu dans les 30 secondes** :
-  Email de : `noreply@infpf.fr`
-  Sujet : " Erreur 404 signalée sur INFPF"
-  Contenu HTML formaté avec :
  - Nom du visiteur
  - Email du visiteur
  - URL de l'erreur
  - Message complet
  - Code erreur
  - Date/heure

#### 2.3 Test Reply-To
```
👉 Dans l'email reçu, cliquez "Répondre"
```
**Attendu** :
-  Le destinataire est automatiquement l'email du visiteur (pas noreply@infpf.fr)

** VALIDATION** : Cochez si vous avez reçu l'email et le Reply-To fonctionne

---

###  TEST 3 : Sentry (Monitoring Erreurs) (3 min)

**Objectif** : Vérifier que Sentry capture les erreurs

#### 3.1 Générer une Erreur de Test
```
👉 https://dev.infpf.fr/test-sentry.php
```
**Attendu** :
-  Page affiche " Erreur envoyée à Sentry !"
-  Message : "Test Sentry - Erreur volontaire générée le [date]"

#### 3.2 Vérifier dans Sentry
```
👉 https://sentry.io/organizations/infpf/projects/php-symfony/
```
1. Actualisez la page (F5)
2. **Attendu** :
   -  L'erreur de test apparaît dans la liste
   -  Titre : "Test Sentry - Erreur volontaire générée le..."
   -  Environnement : `prod`
   -  Détails complets (stack trace, user agent, IP, etc.)

#### 3.3 Nettoyer
```bash
# Supprimer le fichier de test (IMPORTANT !)
rm /home/u665392393/domains/infpf.fr/dev/public/test-sentry.php
```

** VALIDATION** : Cochez si l'erreur apparaît dans Sentry

---

###  TEST 4 : Rate Limiting (Protection DDoS) (3 min)

**Objectif** : Vérifier que le rate limiting bloque après 5 requêtes

#### 4.1 Test avec cURL (Terminal SSH)
```bash
cd /home/u665392393/domains/infpf.fr/dev

for i in {1..6}; do
  echo "=== Requête $i/6 ==="
  curl -X POST https://dev.infpf.fr/contactez-nous \
    -d "nom=Test&email=test@test.com&message=Test" \
    -s -o /dev/null -w "HTTP: %{http_code}\n" \
    -D - | grep -E "HTTP|X-RateLimit"
  sleep 1
done
```

**Attendu** :
```
=== Requête 1/6 ===
HTTP/2 200
X-RateLimit-Limit: 5
X-RateLimit-Remaining: 4

=== Requête 2/6 ===
HTTP/2 200
X-RateLimit-Remaining: 3

=== Requête 3/6 ===
HTTP/2 200
X-RateLimit-Remaining: 2

=== Requête 4/6 ===
HTTP/2 200
X-RateLimit-Remaining: 1

=== Requête 5/6 ===
HTTP/2 200
X-RateLimit-Remaining: 0

=== Requête 6/6 ===
HTTP/2 429  ← BLOQUÉ !
X-RateLimit-Remaining: 0
```

** VALIDATION** : Cochez si la 6ème requête est bloquée (HTTP 429)

---

###  TEST 5 : Backup BDD (2 min)

**Objectif** : Vérifier que le script de backup fonctionne

#### 5.1 Exécuter le Backup
```bash
cd /home/u665392393/domains/infpf.fr/dev
./bin/backup-database.sh
```

**Attendu** :
```
[2025-11-05 17:45:00] Starting database backup...
[2025-11-05 17:45:02] Backup completed successfully
[2025-11-05 17:45:02] File: /home/u665392393/backups/infpf_backup_20251105_174500.sql.gz
[2025-11-05 17:45:02] Size: 2.5M
```

#### 5.2 Vérifier le Fichier
```bash
ls -lh /home/u665392393/backups/ | tail -5
```

**Attendu** :
-  Un fichier `.sql.gz` avec la date du jour
-  Taille > 1 Mo (votre BDD compressée)

#### 5.3 Vérifier les Logs
```bash
cat /home/u665392393/backups/backup.log | tail -5
```

**Attendu** :
```
[2025-11-05 17:45:00] Starting database backup...
[2025-11-05 17:45:02] Backup completed successfully
[2025-11-05 17:45:02] File: infpf_backup_20251105_174500.sql.gz
[2025-11-05 17:45:02] Size: 2621440 bytes (2.5M)
```

** VALIDATION** : Cochez si le backup est créé et le log OK

---

###  TEST 6 : SSL/HTTPS Headers (1 min)

**Objectif** : Vérifier les headers de sécurité HTTPS

#### 6.1 Test Headers
```
👉 https://dev.infpf.fr/test-headers.php
```
OU en ligne de commande :
```bash
curl -I https://dev.infpf.fr/test-headers.php | grep -i "strict-transport\|x-frame\|x-content\|x-xss"
```

**Attendu** :
```
strict-transport-security: max-age=31536000; includeSubDomains
x-frame-options: SAMEORIGIN
x-content-type-options: nosniff
x-xss-protection: 1; mode=block
```

** VALIDATION** : Cochez si tous les headers sont présents

---

###  TEST 7 : Scan Vulnérabilités (1 min)

**Objectif** : Vérifier que Dependabot est actif

#### 7.1 Vérifier GitHub
```
👉 https://github.com/ElyesGhouaiel/infpf-site/security/dependabot
```

**Attendu** :
-  Dependabot configuré
-  Configuration : `composer` + `weekly` updates
-  Aucune alerte critique (ou alertes listées)

** VALIDATION** : Cochez si Dependabot est actif

---

###  TEST 8 : Tests PHPUnit (2 min)

**Objectif** : Vérifier que tous les tests passent

```bash
cd /home/u665392393/domains/infpf.fr/dev
php bin/phpunit
```

**Attendu** :
```
PHPUnit 9.x.x

Testing 
..................................................  50 / XX ( XX%)
..................................................  XX / XX (100%)

Time: XX.XX seconds, Memory: XX.XX MB

OK (XX tests, XX assertions)
```

** VALIDATION** : Cochez si tous les tests passent (OK)

---

###  TEST 9 : Logs Structurés (1 min)

**Objectif** : Vérifier que les logs JSON sont bien générés

```bash
cd /home/u665392393/domains/infpf.fr/dev
tail -20 var/log/prod.log | head -5
```

**Attendu** : Logs au format JSON
```json
{
  "message": "...",
  "context": {...},
  "level": 200,
  "level_name": "INFO",
  "channel": "app",
  "datetime": "2025-11-05T17:45:00.000000+01:00"
}
```

** VALIDATION** : Cochez si les logs sont en JSON

---

##  CHECKLIST FINALE

Cochez au fur et à mesure :

### Pages d'Erreur
- [ ] Page 404 s'affiche correctement
- [ ] Page 500 s'affiche correctement
- [ ] Page 403 s'affiche correctement
- [ ] Design moderne et professionnel

### Formulaires d'Erreur
- [ ] Formulaire 404 envoie sans mailto:
- [ ] Email reçu dans elyes@xeilos.fr
- [ ] Reply-To fonctionne (répondre au visiteur)
- [ ] Pas de popup "not secure"

### Sentry
- [ ] Erreur de test apparaît dans Sentry
- [ ] Dashboard accessible
- [ ] Détails complets (stack trace, etc.)

### Rate Limiting
- [ ] Headers X-RateLimit présents
- [ ] 6ème requête bloquée (HTTP 429)
- [ ] Headers décrémentent correctement

### Backup BDD
- [ ] Script s'exécute sans erreur
- [ ] Fichier .sql.gz créé
- [ ] Log backup.log mis à jour
- [ ] Taille cohérente (> 1 Mo)

### SSL/HTTPS
- [ ] Header HSTS présent
- [ ] Headers sécurité présents (X-Frame-Options, etc.)
- [ ] max-age=31536000 (1 an)

### Scan Vulnérabilités
- [ ] Dependabot configuré
- [ ] Scan hebdomadaire actif

### Tests PHPUnit
- [ ] Tous les tests passent (OK)
- [ ] Aucune erreur critique

### Logs
- [ ] Logs au format JSON
- [ ] Logs structurés lisibles

---

##  RÉSULTAT ATTENDU

**Si tout est coché** :  **JOUR 1 & 2 = 100% FONCTIONNELS !**

Vous avez maintenant :
-  Un site **sécurisé** (Rate Limiting, SSL, Scan)
-  Un site **surveillé** (Sentry, Logs)
-  Un site **professionnel** (Pages d'erreur modernes)
-  Un site **sauvegardé** (Backups quotidiens)
-  Un site **testé** (PHPUnit 100%)
-  Un site **communicatif** (Formulaires email directs)

---

##  PROCHAINE ÉTAPE : JOUR 3

Une fois que tout est validé , on passe au **JOUR 3** :

### Tâches JOUR 3 (2-3 heures)
1. **UptimeRobot** : Surveillance 24/7 du site
2. **Google Analytics / Matomo** : Statistiques visiteurs

**Deadline** : Fin novembre (encore 25 jours !)  
**Estimation** : 5-6 jours pour finir tout le projet

---

## 📞 Support

Si un test échoue :
1. Notez le numéro du test (ex: "TEST 4 : Rate Limiting")
2. Copiez l'erreur exacte
3. Contactez-moi avec ces infos

**Email** : elyes@xeilos.fr

---

**Date de ce guide** : 5 novembre 2025, 17h45  
**Branche** : `feature/performance-security-seo-optimization`

