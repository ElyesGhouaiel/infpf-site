# Politique de Sécurité

## Signalement de Vulnérabilités

La sécurité de notre site est une priorité absolue. Si vous découvrez une vulnérabilité de sécurité, nous vous demandons de nous la signaler de manière responsable.

### Comment signaler une vulnérabilité ?

**NE PUBLIEZ PAS** la vulnérabilité publiquement (GitHub Issues, réseaux sociaux, etc.).

Envoyez un email à : **security@infpf.fr**

Incluez dans votre rapport :
- Description détaillée de la vulnérabilité
- Étapes pour reproduire le problème
- Impact potentiel
- Suggestions de correction (si vous en avez)
- Votre nom/pseudo (pour les remerciements)

### Délai de réponse

- **Accusé de réception** : Sous 48 heures
- **Évaluation initiale** : Sous 7 jours
- **Correction déployée** : Selon la gravité (1-30 jours)

### Reconnaissance

Nous remercions publiquement (avec votre accord) les chercheurs en sécurité qui nous aident à améliorer la sécurité du site.

---

## Versions Supportées

| Version | Support Sécurité |
|---------|------------------|
| 3.x.x   | Support actif |
| 2.x.x   | Support limité (6 mois) |
| 1.x.x   | Non supporté |
| < 1.0   | Non supporté |

---

## Mesures de Sécurité en Place

### Infrastructure
- HTTPS obligatoire (HSTS activé)
- Certificat SSL/TLS valide
- Serveur web sécurisé (Apache + mod_security)
- Firewall applicatif (WAF)

### Application
- Symfony 6.4 (framework sécurisé)
- Headers HTTP de sécurité (CSP, X-Frame-Options, etc.)
- Protection CSRF sur tous les formulaires
- Validation des entrées utilisateur
- Échappement des sorties (XSS prevention)
- Requêtes préparées (SQL injection prevention)
- Rate Limiting sur les endpoints sensibles
- reCAPTCHA v3 sur les formulaires

### Authentification
- Hachage sécurisé des mots de passe (bcrypt)
- Sessions sécurisées (httpOnly, secure, sameSite)
- Protection contre le brute force
- Logout sécurisé

### Monitoring
- Sentry pour le tracking des erreurs
- Logs de sécurité (tentatives de connexion, etc.)
- Monitoring de disponibilité (UptimeRobot)
- Alertes automatiques en cas d'incident

### Données
- Sauvegardes automatiques quotidiennes
- Chiffrement des données sensibles
- Conformité RGPD
- Politique de rétention des données

---

## Que faire en cas d'incident ?

Si vous pensez que votre compte a été compromis :

1. **Changez immédiatement votre mot de passe**
2. **Déconnectez-vous de tous les appareils**
3. **Contactez-nous** : security@infpf.fr
4. **Vérifiez vos emails** pour toute activité suspecte

---

## Ressources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Symfony Security Best Practices](https://symfony.com/doc/current/security.html)
- [ANSSI - Recommandations de sécurité](https://www.ssi.gouv.fr/)

---

## Contact

- **Email sécurité** : security@infpf.fr
- **Email général** : contact@infpf.fr
- **Site web** : https://infpf.fr

---

*Dernière mise à jour : 20 novembre 2025*
