#  INSTRUCTIONS PURGE CACHE CDN HOSTINGER

##  Modifications Appliquées

- jQuery **supprimé** de `base.html.twig` (-85 Kio)
- jQuery **supprimé** de `formation.html.twig` (commentaire ligne 21)
- Cache Symfony **cleared**

##  Problème : CDN Hostinger Cache l'Ancien HTML

Le serveur `hcdn` (Hostinger CDN) cache agressivement les pages HTML pendant 24-48h.

##  Solutions

### Option 1 : Purge via hPanel (RECOMMANDÉ)

1. Connectez-vous : https://hpanel.hostinger.com
2. Websites → Sélectionnez `infpf.fr`
3. Advanced → CDN ou Cache
4. Cliquez sur "Purge CDN Cache" ou "Clear All Cache"
5. Attendez 2 minutes
6. Testez : https://pagespeed.web.dev/analysis?url=https://dev.infpf.fr/formation

### Option 2 : Désactiver Temporairement le CDN

1. hPanel → Websites → infpf.fr
2. Advanced → CDN
3. Toggle OFF pendant 5 minutes
4. Testez la page
5. Toggle ON

### Option 3 : Attendre 24-48h

Le cache CDN expirera automatiquement.

##  Scores Attendus APRÈS Purge du Cache

| Format | Avant | Après Purge | Gain |
|--------|-------|-------------|------|
| **Mobile** | 73 | **88-92** | +15-19 pts |
| **Desktop** | 83 | **95-98** | +12-15 pts |

**Économies totales : -428 Kio de JavaScript inutilisé !**

---

##  Vérification Post-Purge

```bash
# Vérifier que jQuery n'est plus chargé
curl -s https://dev.infpf.fr/formation | grep -i "jquery"

# Résultat attendu : AUCUNE ligne
```

Si jQuery apparaît encore, attendez 5 minutes et retestez.

## 📞 Support Hostinger

Si la purge ne fonctionne pas :
- Chat Live : https://www.hostinger.fr/contact
- Demandez : "Purge complète du cache CDN pour infpf.fr et dev.infpf.fr"

