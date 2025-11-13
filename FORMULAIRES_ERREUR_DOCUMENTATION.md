# ✉️ Documentation : Formulaires des Pages d'Erreur

**Date** : 5 novembre 2025, 17h30  
**Auteur** : Système automatisé  
**Version** : 1.0

---

## 🎯 Problème Résolu

### ❌ Avant

Les formulaires sur les pages d'erreur utilisaient `mailto:elyes@xeilos.fr` :
- ❌ Ouvrait le client email local (Outlook, Gmail, etc.)
- ❌ Popup "The information you're about to submit is not secure"
- ❌ Ne fonctionnait pas si l'utilisateur n'avait pas de client email configuré
- ❌ Pas d'historique des signalements
- ❌ Mauvaise expérience utilisateur

### ✅ Maintenant

Les formulaires envoient **directement** via le serveur :
- ✅ Envoi AJAX invisible pour l'utilisateur
- ✅ Pas de popup de sécurité
- ✅ Fonctionne toujours (même sans client email)
- ✅ Logs des signalements dans Monolog
- ✅ Emails HTML formatés professionnellement
- ✅ Feedback visuel (succès/erreur)
- ✅ Expérience utilisateur moderne

---

## 📋 Ce Qui A Été Modifié

### 1. Nouveau Contrôleur Symfony

**Fichier** : `src/Controller/ErrorReportController.php`

**Route** : `POST /report-error`

**Fonctionnalités** :
- Validation des champs (nom, email, message)
- Envoi d'email HTML formaté
- Logging des signalements
- Réponses JSON pour AJAX

**Code clé** :
```php
#[Route('/report-error', name: 'app_report_error', methods: ['POST'])]
public function reportError(
    Request $request,
    MailerInterface $mailer,
    LoggerInterface $logger
): Response {
    // Récupération et validation des données
    // Envoi de l'email
    // Logging
    // Retour JSON
}
```

---

### 2. Templates d'Erreur Modifiés

#### Page 404 - `error404.html.twig`

**Avant** :
```html
<form action="mailto:elyes@xeilos.fr" method="post" enctype="text/plain">
```

**Après** :
```html
<form id="errorReportForm" class="contact-form">
    <!-- Champs du formulaire -->
    <button type="submit" id="submitBtn">
        <span id="btnText">Envoyer le message</span>
        <span id="btnLoader" style="display: none;">Envoi en cours...</span>
    </button>
</form>

<script>
    // JavaScript pour envoi AJAX
    document.getElementById('errorReportForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        // Envoi vers /report-error
        // Affichage du résultat
    });
</script>
```

#### Page 500 - `error500.html.twig`

Même système que la page 404, avec `error_code: '500'`

#### Page 403 - `error403.html.twig`

**Avant** : Simple lien `mailto:`

**Après** : Formulaire complet + AJAX (comme 404 et 500)

**Nouveau** : Ajout des styles CSS pour le formulaire (n'existait pas avant)

---

## 🎨 Format de l'Email Reçu

Lorsqu'un visiteur signale une erreur, vous recevez un email HTML formaté :

```
De : noreply@infpf.fr
Répondre à : [email du visiteur]
À : elyes@xeilos.fr
Sujet : 🔴 Erreur 404 signalée sur INFPF

┌─────────────────────────────────────────┐
│       🔴 Erreur Signalée                │
│       Code d'erreur : 404                │
└─────────────────────────────────────────┘

👤 Nom du visiteur
   Jean Dupont

📧 Email de contact
   jean.dupont@exemple.fr

🔗 URL de l'erreur
   https://dev.infpf.fr/page-introuvable

💬 Message du visiteur
   Je cherchais la page des formations
   mais je suis tombé sur une erreur 404.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Email envoyé automatiquement depuis infpf.fr
Date : 05/11/2025 à 17:30:00
```

**Avantages** :
- ✅ Email formaté professionnel
- ✅ Toutes les infos importantes visibles d'un coup d'œil
- ✅ Réponse directe possible (Reply-To configuré)
- ✅ URL cliquable pour reproduire l'erreur

---

## 🧪 Comment Tester

### Test 1 : Via l'Interface (Recommandé)

1. **Accédez à une page d'erreur** :
   ```
   https://dev.infpf.fr/test/error/404
   https://dev.infpf.fr/test/error/500
   https://dev.infpf.fr/test/error/403
   ```

2. **Remplissez le formulaire** :
   - Nom : Votre nom
   - Email : Votre email
   - Message : "Test du nouveau système d'envoi"

3. **Cliquez sur "Envoyer"**

4. **Observez** :
   - Le bouton affiche "Envoi en cours..."
   - Après ~1-2 secondes, un message vert apparaît :
     ```
     ✅ Votre message a été envoyé avec succès ! 
        Nous vous recontacterons rapidement.
     ```
   - Le formulaire se vide automatiquement

5. **Vérifiez votre boîte mail** (`elyes@xeilos.fr`) :
   - Vous devriez recevoir l'email dans ~30 secondes

---

### Test 2 : Via cURL (Technique)

```bash
cd /home/u665392393/domains/infpf.fr/dev

curl -X POST https://dev.infpf.fr/report-error \
  -d "name=Test User" \
  -d "email=test@example.com" \
  -d "message=Ceci est un test du système" \
  -d "error_code=404" \
  -d "error_url=https://dev.infpf.fr/test/error/404"
```

**Résultat attendu** :
```json
{
  "success": true,
  "message": "Votre message a été envoyé avec succès ! Nous vous recontacterons rapidement."
}
```

**Si erreur** :
```json
{
  "success": false,
  "message": "Tous les champs sont obligatoires."
}
```

---

### Test 3 : Vérifier les Logs

```bash
cd /home/u665392393/domains/infpf.fr/dev

# Voir les derniers signalements d'erreur
tail -20 var/log/prod.log | grep "Erreur signalée"
```

**Résultat attendu** :
```json
{
  "message": "Erreur signalée",
  "context": {
    "error_code": "404",
    "reporter_email": "test@example.com",
    "reporter_name": "Test User"
  },
  "level": 200,
  "level_name": "INFO",
  "channel": "app",
  "datetime": "2025-11-05T17:30:00.000000+01:00"
}
```

---

## 🔍 Détails Techniques

### Validation Côté Serveur

Le contrôleur valide **toutes** les entrées :

1. **Champs obligatoires** :
   - `name` : Non vide
   - `email` : Non vide + format email valide
   - `message` : Non vide

2. **Validation email** :
   ```php
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       return $this->json([
           'success' => false,
           'message' => 'Email invalide.'
       ], Response::HTTP_BAD_REQUEST);
   }
   ```

3. **Données supplémentaires** :
   - `error_code` : 404, 500, 403, etc.
   - `error_url` : URL de la page d'erreur

---

### Sécurité

✅ **Protections en place** :

1. **Rate Limiting** : Même limites que le formulaire de contact principal
   - 5 signalements maximum par 15 minutes
   - Header `X-RateLimit-Remaining` dans la réponse

2. **Validation stricte** : Tous les champs validés côté serveur

3. **Pas d'injection HTML** : Email HTML généré côté serveur (pas de contenu utilisateur brut)

4. **Reply-To sécurisé** : Email de réponse configuré sur l'email du visiteur

5. **Logging** : Tous les signalements sont enregistrés pour audit

---

### JavaScript AJAX

Le formulaire utilise `fetch()` moderne (pas de jQuery) :

```javascript
const formData = new FormData();
formData.append('name', document.getElementById('name').value);
formData.append('email', document.getElementById('email').value);
formData.append('message', document.getElementById('message').value);
formData.append('error_code', '404');
formData.append('error_url', window.location.href);

const response = await fetch('/report-error', {
    method: 'POST',
    body: formData
});

const data = await response.json();

if (data.success) {
    // Afficher message de succès
    formMessage.textContent = '✅ ' + data.message;
    form.reset();
} else {
    // Afficher message d'erreur
    formMessage.textContent = '❌ ' + data.message;
}
```

**Avantages** :
- ✅ Compatible tous navigateurs modernes
- ✅ Async/await pour code lisible
- ✅ Gestion erreurs réseau
- ✅ Feedback visuel immédiat

---

## 📊 Statistiques & Monitoring

### Logs Monolog

Tous les signalements sont enregistrés dans `var/log/prod.log` :

```bash
# Compter les signalements d'erreur aujourd'hui
grep "Erreur signalée" var/log/prod.log | grep "$(date +%Y-%m-%d)" | wc -l

# Voir les erreurs les plus signalées
grep "error_code" var/log/prod.log | grep -o '"error_code":"[0-9]*"' | sort | uniq -c | sort -rn
```

### Sentry

Si une erreur se produit **pendant** l'envoi de l'email, elle est capturée par Sentry :
- Erreur SMTP
- Erreur de validation
- Erreur serveur

**Dashboard Sentry** : https://sentry.io/organizations/infpf/projects/php-symfony/

---

## 🎯 Avantages du Nouveau Système

### Pour les Visiteurs

| Avant (mailto:) | Après (AJAX) |
|-----------------|--------------|
| ❌ Popup "not secure" | ✅ Envoi silencieux |
| ❌ Client email requis | ✅ Fonctionne toujours |
| ❌ Pas de feedback | ✅ Message de confirmation |
| ❌ Redirection hors site | ✅ Reste sur la page |

### Pour l'Administrateur

| Avant | Après |
|-------|-------|
| ❌ Emails bruts | ✅ Emails HTML formatés |
| ❌ Pas de logs | ✅ Logs structurés |
| ❌ Pas de stats | ✅ Stats dans Sentry/Monolog |
| ❌ Pas de monitoring | ✅ Monitoring actif |

---

## 🔧 Configuration Mailer

Pour que l'envoi d'email fonctionne, vérifiez `MAILER_DSN` dans `.env.local` :

```env
# Exemple avec SMTP
MAILER_DSN=smtp://username:password@smtp.example.com:587

# Exemple avec Gmail
MAILER_DSN=gmail+smtp://username:password@default

# Exemple avec Mailgun
MAILER_DSN=mailgun+smtp://username:password@default
```

**Test de configuration** :
```bash
php bin/console debug:container mailer
```

---

## ❓ FAQ

### Q : Les emails arrivent dans les spams ?

**R** : Configurez SPF/DKIM pour `infpf.fr`. Ajoutez dans votre DNS :

```
v=spf1 include:_spf.google.com ~all
```

---

### Q : Combien de temps pour recevoir l'email ?

**R** : En moyenne 10-30 secondes. Si > 2 minutes, vérifiez :
1. Configuration `MAILER_DSN`
2. Logs : `tail var/log/prod.log`
3. Serveur SMTP disponible

---

### Q : Peut-on changer l'email destinataire ?

**R** : Oui, modifiez dans `src/Controller/ErrorReportController.php` :

```php
$emailMessage = (new Email())
    ->from('noreply@infpf.fr')
    ->to('VOTRE_EMAIL@example.com')  // ← ICI
    ->replyTo($email)
    ->subject("🔴 Erreur {$errorCode} signalée sur INFPF")
```

---

### Q : Comment désactiver temporairement ?

**R** : Commentez la route dans le contrôleur :

```php
// #[Route('/report-error', name: 'app_report_error', methods: ['POST'])]
```

Puis videz le cache : `php bin/console cache:clear`

---

## 📚 Fichiers Modifiés

| Fichier | Changements |
|---------|-------------|
| `src/Controller/ErrorReportController.php` | **NOUVEAU** - Contrôleur envoi email |
| `templates/bundles/TwigBundle/Exception/error404.html.twig` | Formulaire AJAX + JavaScript |
| `templates/bundles/TwigBundle/Exception/error500.html.twig` | Formulaire AJAX + JavaScript |
| `templates/bundles/TwigBundle/Exception/error403.html.twig` | **NOUVEAU FORMULAIRE** + CSS + JavaScript |

---

## ✅ Checklist de Validation

- [ ] Page 404 : Formulaire envoie sans `mailto:`
- [ ] Page 500 : Formulaire envoie sans `mailto:`
- [ ] Page 403 : Formulaire envoie sans `mailto:`
- [ ] Message de succès affiché après envoi
- [ ] Email reçu dans la boîte `elyes@xeilos.fr`
- [ ] Format HTML de l'email correct
- [ ] Logs présents dans `var/log/prod.log`
- [ ] Pas de popup "not secure"
- [ ] Bouton disabled pendant envoi
- [ ] Formulaire réinitialisé après succès

---

**Dernière mise à jour** : 5 novembre 2025, 17h35  
**Contact** : elyes@xeilos.fr

