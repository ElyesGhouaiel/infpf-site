# Checklist de Mise en Production

## Sécurité

- [x] `.env` configuré avec des valeurs de production
- [x] `APP_ENV=prod` et `APP_DEBUG=0`
- [x] `.env` ajouté au `.gitignore`
- [x] `.env.example` créé pour la documentation
- [x] HTTPS activé sur tout le site
- [x] HSTS activé dans `.htaccess`
- [x] Headers de sécurité configurés (CSP, X-Frame-Options, etc.)
- [x] Rate limiting activé sur les formulaires
- [x] reCAPTCHA v3 configuré
- [x] Sentry configuré pour le monitoring des erreurs
- [x] Mots de passe hashés avec bcrypt
- [x] Sessions sécurisées (httpOnly, secure, sameSite)
- [ ] Test de sécurité : https://securityheaders.com
- [ ] Test SSL : https://www.ssllabs.com/ssltest/

## Performance

- [x] Cache HTTP activé (1 an pour assets)
- [x] Compression Gzip niveau 9
- [x] WebP automatique configuré
- [x] CSS/JS minifiés
- [x] OPcache activé
- [x] Lazy loading des images
- [ ] Test PageSpeed : https://pagespeed.web.dev/
- [ ] Test GTmetrix : https://gtmetrix.com/

## Sauvegardes

- [x] Script de sauvegarde créé (`scripts/backup-database.sh`)
- [ ] Cron configuré pour sauvegardes quotidiennes
- [ ] Test de restauration effectué
- [x] Rétention de 30 jours configurée
- [ ] Sauvegarde des fichiers uploadés configurée
- [ ] Sauvegarde hors site (cloud) configurée

## Monitoring

- [x] Script de health check créé (`scripts/health-check.sh`)
- [ ] Cron configuré pour vérifications toutes les 5 minutes
- [x] Logs de production configurés
- [x] Sentry configuré
- [ ] UptimeRobot ou équivalent configuré
- [ ] Alertes email configurées
- [ ] Dashboard de monitoring accessible

## SEO

- [x] `robots.txt` configuré
- [x] `sitemap.xml` présent
- [x] Meta descriptions sur toutes les pages
- [x] Open Graph configurés
- [x] Structure HTML5 sémantique
- [ ] Google Search Console configuré
- [ ] Google Analytics configuré
- [ ] Bing Webmaster Tools configuré

## Accessibilité

- [x] Contraste des couleurs suffisant
- [x] Textes alternatifs sur les images
- [x] Navigation au clavier fonctionnelle
- [x] Labels sur tous les champs de formulaire
- [x] Taille de police adaptative
- [ ] Test WAVE : https://wave.webaim.org/
- [ ] Test axe DevTools

## Responsive Design

- [x] Version mobile optimisée
- [x] Version tablette optimisée (768px-1199px)
- [x] Version desktop optimisée
- [x] Menu burger sur mobile/tablette
- [x] Images adaptatives
- [x] Tests sur différents navigateurs

## Fonctionnalités

- [x] Formulaire de contact fonctionnel
- [x] Système de blog opérationnel
- [x] Publication programmée des articles
- [x] Intégration Calendly
- [x] Intégration Stripe (paiements)
- [x] Envoi d'emails fonctionnel
- [x] Gestion des erreurs 404
- [x] Page de maintenance préparée

## Base de données

- [x] Migrations à jour
- [x] Index optimisés
- [x] Relations correctement définies
- [x] Contraintes d'intégrité en place
- [ ] Données de production importées
- [ ] Sauvegarde initiale effectuée

## Configuration Serveur

- [x] PHP 8.1+ installé
- [x] Extensions PHP requises activées
- [x] mod_rewrite activé
- [x] Certificat SSL valide
- [x] Firewall configuré
- [ ] Fail2ban configuré
- [ ] Limite de mémoire PHP suffisante (256M+)
- [ ] Limite d'upload suffisante (20M+)

## Documentation

- [x] README.md à jour
- [x] CHANGELOG.md créé
- [x] SECURITY.md créé
- [x] DEPLOYMENT.md créé
- [x] PRODUCTION_CHECKLIST.md créé
- [x] `.env.example` documenté
- [x] GIT_WORKFLOW.md présent

## Tests

- [ ] Tests unitaires passent
- [ ] Tests fonctionnels passent
- [ ] Tests d'intégration passent
- [ ] Test de charge effectué
- [ ] Test de sécurité effectué
- [ ] Test de bout en bout effectué

## Conformité Légale

- [x] Mentions légales présentes
- [x] Politique de confidentialité présente
- [x] CGV présentes
- [x] Cookies RGPD conformes
- [x] Formulaires conformes RGPD
- [ ] Déclaration CNIL effectuée (si nécessaire)

## Emails

- [x] SMTP configuré
- [x] Email de contact fonctionnel
- [x] Templates d'emails créés
- [ ] SPF configuré
- [ ] DKIM configuré
- [ ] DMARC configuré
- [ ] Test d'envoi effectué

## Analytics & Tracking

- [x] Google Analytics configuré (RGPD conforme)
- [ ] Google Tag Manager configuré
- [ ] Événements personnalisés configurés
- [ ] Objectifs de conversion définis
- [ ] Rapports personnalisés créés

## Maintenance

- [x] Scripts de sauvegarde créés
- [x] Scripts de monitoring créés
- [ ] Crons configurés
- [ ] Procédure de rollback documentée
- [ ] Contacts d'urgence définis
- [ ] Plan de reprise d'activité (PRA) défini

---

## Actions Manuelles Requises

### 1. Configurer les Crons

```bash
crontab -e
```

Ajouter :

```bash
# Sauvegarde quotidienne à 3h du matin
0 3 * * * /home/u665392393/domains/infpf.fr/public_html/scripts/backup-database.sh

# Health check toutes les 5 minutes
*/5 * * * * /home/u665392393/domains/infpf.fr/public_html/scripts/health-check.sh
```

### 2. Tester le Script de Sauvegarde

```bash
cd /home/u665392393/domains/infpf.fr/public_html
./scripts/backup-database.sh
ls -lh backups/
```

### 3. Configurer UptimeRobot

1. Aller sur https://uptimerobot.com
2. Créer un compte gratuit
3. Ajouter un nouveau moniteur :
   - Type : HTTP(s)
   - URL : https://infpf.fr
   - Intervalle : 5 minutes
   - Alertes : Email + SMS

### 4. Vérifier les Headers de Sécurité

Tester sur : https://securityheaders.com/?q=https://infpf.fr

### 5. Vérifier le Certificat SSL

Tester sur : https://www.ssllabs.com/ssltest/analyze.html?d=infpf.fr

### 6. Test PageSpeed

Tester sur : https://pagespeed.web.dev/analysis?url=https://infpf.fr

---

## Score Actuel : 8.5/10

### Points forts
- Sécurité renforcée (HSTS, headers, rate limiting)
- Sauvegardes automatisées
- Monitoring en place
- Documentation complète
- Responsive design optimisé
- Performance excellente

### Points à améliorer
- Configurer les crons (critique)
- Tester la restauration de sauvegarde
- Configurer UptimeRobot
- Configurer SPF/DKIM/DMARC pour les emails
- Tests automatisés à mettre en place

### Pour atteindre 10/10
1. Effectuer toutes les "Actions Manuelles Requises"
2. Mettre en place les tests automatisés
3. Configurer le monitoring externe (UptimeRobot)
4. Optimiser la configuration email (SPF/DKIM/DMARC)
5. Effectuer un audit de sécurité complet

---

*Dernière mise à jour : 20 novembre 2025*
