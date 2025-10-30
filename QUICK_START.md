# 🚀 Quick Start - Git Workflow INFPF

## 🎯 Réponse à votre question

> **"Comment merger dev vers prod (main) sans que dev disparaisse ?"**

**Réponse : La branche dev ne disparaîtra JAMAIS !**

`git merge` = **COPIER** les changements, pas **DÉPLACER**
- dev reste intacte après le merge
- Le sous-domaine dev.infpf.fr continue de fonctionner
- Vous pouvez continuer à développer sur dev immédiatement après

---

## 📊 Schéma Simple

```
1️⃣ Créer une feature
   dev → git checkout -b feature/ma-feature
   
2️⃣ Développer et tester
   feature/ma-feature → Tester sur dev.infpf.fr
   
3️⃣ Merger dans dev
   feature/ma-feature → dev (merge) → dev existe toujours ✅
   
4️⃣ Déployer en prod
   dev → main (merge) → dev existe toujours ✅
                      → dev.infpf.fr fonctionne toujours ✅
```

---

## ⚡ Commandes Essentielles

### Créer une nouvelle fonctionnalité
```bash
cd /home/u665392393/domains/infpf.fr/public_html
./new-feature.sh nom-de-ma-feature
```

### Déployer dev → production
```bash
cd /home/u665392393/domains/infpf.fr/public_html
./deploy-to-prod.sh "Description du déploiement"
```

---

## 📁 Où Travailler

| Environnement | Branche | URL | Dossier |
|--------------|---------|-----|---------|
| **Développement** | `dev` | https://dev.infpf.fr/ | `/dev/` |
| **Production** | `main` | https://infpf.fr/ | `/public_html/` |

---

## ✅ Ce qui ne disparaît JAMAIS

✅ La branche `dev`  
✅ Le dossier `/home/u665392393/domains/infpf.fr/dev/`  
✅ Le sous-domaine `dev.infpf.fr`  
✅ Votre capacité à continuer à développer

---

## 📚 Documentation Complète

- **Guide complet** : `GIT_WORKFLOW.md`
- **Workflow avancé** : `WORKFLOW.md`

---

*Vous pouvez merger dev vers main autant de fois que vous voulez, dev restera toujours là ! 🎉*

