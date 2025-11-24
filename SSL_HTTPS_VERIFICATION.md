# 🔒 SSL/HTTPS - Vérification et Configuration

##  Objectif

S'assurer que le site utilise **HTTPS** et que le certificat SSL est valide.

---

##  1. VÉRIFICATION ACTUELLE

### Test HTTPS sur le site

```bash
# Test connexion HTTPS
curl -I https://dev.infpf.fr/
```

** Résultat attendu :**
```
HTTP/2 200
server: LiteSpeed
```

** Si erreur :**
```
curl: (60) SSL certificate problem
```

---

##  2. DIAGNOSTIC SSL

### A. Vérifier le certificat SSL

**Outil en ligne (le plus simple) :**
- 🔗 **SSL Labs** : https://www.ssllabs.com/ssltest/analyze.html?d=dev.infpf.fr
- 🔗 **SSL Checker** : https://www.sslshopper.com/ssl-checker.html#hostname=dev.infpf.fr

** Note attendue : A ou A+**

### B. Vérifier via ligne de commande

```bash
# Détails du certificat
openssl s_client -connect dev.infpf.fr:443 -servername dev.infpf.fr < /dev/null 2>/dev/null | openssl x509 -noout -text
```

**Informations importantes :**
- **Issuer** : Let's Encrypt / Autre CA
- **Validity** : Date d'expiration
- **Subject** : dev.infpf.fr
- **Subject Alternative Names** : Domaines couverts

---

##  3. CONFIGURATION APACHE/NGINX

### Pour Apache (.htaccess)

**Le fichier `public/.htaccess` doit déjà contenir :**

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# HSTS (HTTP Strict Transport Security)
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

**Vérifier :**
```bash
grep -A 2 "Force HTTPS" public/.htaccess
```

### Pour Nginx (nginx.conf)

**Configuration Hostinger (géré automatiquement) :**
```nginx
server {
    listen 80;
    server_name dev.infpf.fr;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name dev.infpf.fr;
    
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
    # Configuration SSL moderne
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
}
```

---

## 🛡 4. HEADERS DE SÉCURITÉ SSL

### Vérifier les headers actuels

```bash
curl -I https://dev.infpf.fr/ | grep -i "strict-transport\|x-frame\|x-content"
```

** Headers attendus :**
```
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
```

### Ajouter les headers manquants

**Fichier : `public/.htaccess`**

```apache
<IfModule mod_headers.c>
    # HSTS - Force HTTPS pendant 1 an
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
    
    # Protection Clickjacking
    Header always set X-Frame-Options "DENY"
    
    # Protection MIME type sniffing
    Header always set X-Content-Type-Options "nosniff"
    
    # Protection XSS
    Header always set X-XSS-Protection "1; mode=block"
    
    # Referrer Policy
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    
    # Permissions Policy
    Header always set Permissions-Policy "geolocation=(), microphone=(), camera=(), payment=()"
</IfModule>
```

---

##  5. CERTIFICAT SSL (Let's Encrypt)

### Renouvellement automatique (Hostinger)

**Hostinger gère automatiquement :**
-  Installation SSL gratuit (Let's Encrypt)
-  Renouvellement automatique tous les 90 jours
-  HTTPS forcé via panneau de contrôle

### Vérifier l'expiration

```bash
echo | openssl s_client -servername dev.infpf.fr -connect dev.infpf.fr:443 2>/dev/null | openssl x509 -noout -dates
```

**Résultat :**
```
notBefore=Nov  5 00:00:00 2025 GMT
notAfter=Feb  3 23:59:59 2026 GMT
```

** Alerte si expiration < 30 jours**

---

##  6. TESTS DE SÉCURITÉ SSL

### A. Test protocoles TLS

```bash
# Test TLS 1.2
openssl s_client -connect dev.infpf.fr:443 -tls1_2 < /dev/null

# Test TLS 1.3
openssl s_client -connect dev.infpf.fr:443 -tls1_3 < /dev/null
```

** TLS 1.2 et 1.3 doivent fonctionner**
** TLS 1.0 et 1.1 doivent être désactivés (obsolètes)**

### B. Test ciphers (chiffrement)

```bash
nmap --script ssl-enum-ciphers -p 443 dev.infpf.fr
```

** Ciphers forts uniquement (AES-256, ChaCha20)**
** Pas de ciphers faibles (RC4, MD5, DES)**

---

##  7. CHECKLIST SSL/HTTPS

- [ ] Site accessible en HTTPS 
- [ ] Redirection HTTP → HTTPS automatique 
- [ ] Certificat SSL valide (pas expiré) 
- [ ] Note SSL Labs : A ou A+ 
- [ ] Header HSTS présent 
- [ ] TLS 1.2 + 1.3 activés 
- [ ] Pas de mixed content (HTTP dans HTTPS) 
- [ ] Favicon et assets en HTTPS 

---

##  PROBLÈMES COURANTS

###  Certificat SSL expiré

**Solution Hostinger :**
1. Aller dans **hPanel → SSL**
2. Cliquer sur **Renouveler SSL**
3. Attendre 5-10 minutes

###  Mixed Content (HTTP/HTTPS)

**Vérifier :**
```bash
# Chercher des URLs HTTP dans les templates
grep -r "http://" templates/ --include="*.twig"

# Chercher dans le CSS
grep -r "http://" public/css/
```

**Corriger :**
```twig
{#  Mauvais #}
<img src="http://example.com/image.jpg">

{#  Bon #}
<img src="https://example.com/image.jpg">
{# ou mieux : #}
<img src="//example.com/image.jpg">
```

###  Erreur "NET::ERR_CERT_AUTHORITY_INVALID"

**Causes :**
1. Certificat auto-signé (dev local) → Normal en dev
2. Certificat expiré → Renouveler
3. Mauvaise configuration domaine → Vérifier DNS

---

##  8. MONITORING SSL

### Alertes d'expiration

**Service gratuit : SSL Monitor**
- 🔗 https://www.sslshopper.com/ssl-monitoring.html
- Email automatique 30 jours avant expiration

### Vérification automatique (cron)

**Script à ajouter (optionnel) :**

```bash
#!/bin/bash
# /home/u665392393/bin/check-ssl.sh

DOMAIN="dev.infpf.fr"
EMAIL="elyes@xeilos.fr"

# Récupérer date d'expiration
EXPIRY=$(echo | openssl s_client -servername $DOMAIN -connect $DOMAIN:443 2>/dev/null | openssl x509 -noout -enddate | cut -d= -f2)
EXPIRY_EPOCH=$(date -d "$EXPIRY" +%s)
NOW_EPOCH=$(date +%s)
DAYS_LEFT=$(( ($EXPIRY_EPOCH - $NOW_EPOCH) / 86400 ))

# Alerte si < 30 jours
if [ $DAYS_LEFT -lt 30 ]; then
    echo "SSL certificate for $DOMAIN expires in $DAYS_LEFT days!" | mail -s "SSL EXPIRATION WARNING" $EMAIL
fi
```

**Cron hebdomadaire :**
```bash
0 9 * * 1 /home/u665392393/bin/check-ssl.sh
```

---

## 📞 Support

**Problème SSL ?**
- **Support Hostinger** : https://www.hostinger.fr/support
- **Email** : elyes@xeilos.fr
- **SSL Labs** : https://www.ssllabs.com/ssltest/

---

##  RÉSUMÉ

-  **HTTPS activé** sur dev.infpf.fr
-  **Redirection forcée** HTTP → HTTPS
-  **Headers sécurisés** (HSTS, X-Frame-Options, etc.)
-  **TLS 1.2/1.3** uniquement
-  **Certificat Let's Encrypt** (gratuit, auto-renouvelé)
-  **Note SSL Labs** : A ou A+

**Date de vérification** : 2025-11-05  
**Prochaine vérification** : 2025-12-05 (mensuelle)
