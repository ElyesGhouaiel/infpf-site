#  FAQ - Workflow Git INFPF

## Questions Résolues

### 1⃣ Pourquoi je ne pouvais pas faire `git checkout dev` ?

**Problème initial** : `error: pathspec 'dev' did not match any file(s) known to git`

**Raison** : La branche `dev` existait sur GitHub (`origin/dev`) mais pas **localement** dans votre dossier `public_html`.

**Solution appliquée** :
```bash
cd /home/u665392393/domains/infpf.fr/public_html
git fetch origin              # Récupérer les branches depuis GitHub
git checkout -b dev origin/dev  # Créer une branche locale dev qui suit origin/dev
```

**Comprendre la différence** :
- **Branches distantes** (`origin/dev`, `origin/main`) : Sur GitHub
- **Branches locales** (`dev`, `main`) : Dans votre dossier Git local

Pour voir toutes les branches :
```bash
git branch -a    # -a pour voir locales ET distantes
```

---

### 2⃣ Main a-t-elle la dernière version de dev ?

**Réponse** : **OUI, maintenant les deux sont identiques !**

**État actuel** :
```
📁 public_html (main) : 1187ff3 - docs: quick start guide
📁 dev (dev)          : 1187ff3 - docs: quick start guide
 Les deux branches sont au MÊME commit
```

**Ce qui a été fait** :
1. Les fichiers de documentation workflow ont été créés sur `main`
2. On a synchronisé `dev` avec `main` : `git merge main`
3. Maintenant `main` et `dev` sont identiques

---

### 3⃣ Comment merger dev vers main sans que dev disparaisse ?

**Réponse courte** : **La branche dev ne disparaîtra JAMAIS !**

**`git merge` = COPIER, pas DÉPLACER**

Quand vous faites :
```bash
cd public_html
git checkout main
git merge dev
```

Git **COPIE** les changements de `dev` vers `main`, mais :
-  La branche `dev` reste intacte
-  Le dossier `/dev/` reste
-  Le sous-domaine `dev.infpf.fr` continue de fonctionner
-  Vous pouvez immédiatement recommencer à développer sur `dev`

**Analogie** :
- `git merge dev` = Copier-coller un document
- ≠ Couper-coller un document

---

### 4⃣ Quelle est la différence entre `/public_html/` et `/dev/` ?

**Deux dossiers, deux environnements, MÊME repo Git** :

| Aspect | `/public_html/` | `/dev/` |
|--------|----------------|---------|
| **Branche active** | `main` | `dev` |
| **URL** | https://infpf.fr/ | https://dev.infpf.fr/ |
| **Usage** | PRODUCTION (clients) | DÉVELOPPEMENT (tests) |
| **Stabilité** |  Toujours stable |  En cours de développement |
| **Mise à jour** | Après validation complète | Régulièrement |

**Workflow** :
```
1. Développer dans /dev/ (branche dev ou feature)
2. Tester sur dev.infpf.fr
3. Quand OK, merger dans main
4. Les changements apparaissent sur infpf.fr (production)
```

---

### 5⃣ Comment savoir sur quelle branche je suis ?

```bash
# Méthode 1 : Voir la branche actuelle
git branch --show-current

# Méthode 2 : Voir toutes les branches (la branche active a un *)
git branch

# Méthode 3 : Voir l'état complet
git status
```

**Recommandation** : Toujours vérifier avant de commiter !

---

### 6⃣ Comment passer d'une branche à l'autre ?

```bash
# Passer sur dev
git checkout dev

# Passer sur main
git checkout main

# Passer sur une feature
git checkout feature/nom-de-ma-feature
```

** Important** : Commitez ou stash vos changements avant de changer de branche !

---

### 7⃣ Quelle est la différence entre les branches locales et distantes ?

**Branches locales** : Dans votre dossier Git sur le serveur
- `main`, `dev`, `feature/xxx`
- Vous travaillez sur ces branches
- Créées avec `git checkout -b nom-branche`

**Branches distantes** : Sur GitHub
- `origin/main`, `origin/dev`, `origin/feature/xxx`
- Synchronisées avec `git push` et `git pull`
- Visibles avec `git branch -r`

**Commandes de synchronisation** :
```bash
# Envoyer vos commits vers GitHub
git push origin nom-branche

# Récupérer les commits depuis GitHub
git pull origin nom-branche

# Voir les nouvelles branches sur GitHub
git fetch origin
```

---

### 8⃣ Comment créer une feature branch ?

**Méthode automatique (recommandée)** :
```bash
cd /home/u665392393/domains/infpf.fr/public_html
./new-feature.sh amelioration-menu
```

**Méthode manuelle** :
```bash
cd /home/u665392393/domains/infpf.fr/dev
git checkout dev
git pull origin dev
git checkout -b feature/amelioration-menu
# ... développer ...
git add .
git commit -m "feat: amélioration du menu"
git push origin feature/amelioration-menu
```

---

### 9⃣ Comment merger une feature dans dev ?

```bash
cd /home/u665392393/domains/infpf.fr/dev
git checkout dev
git merge feature/amelioration-menu
git push origin dev
```

**Note** : La feature branch existe toujours après le merge !

---

### 🔟 Comment déployer dev en production ?

**Méthode automatique (recommandée)** :
```bash
cd /home/u665392393/domains/infpf.fr/public_html
./deploy-to-prod.sh "Description du déploiement"
```

**Méthode manuelle** :
```bash
cd /home/u665392393/domains/infpf.fr/public_html
git checkout main
git merge dev -m "deploy: description"
git push origin main
php bin/console cache:clear --env=prod
```

**Important** : dev ne disparaît pas après le déploiement ! 

---

##  Récapitulatif de Votre Situation Actuelle

 **Problème résolu** : Vous pouvez maintenant switcher entre `main` et `dev`

 **Branches synchronisées** : `main` et `dev` sont au même commit `1187ff3`

 **Environnements configurés** :
- Production : `/public_html/` (branche `main`) → https://infpf.fr/
- Développement : `/dev/` (branche `dev`) → https://dev.infpf.fr/

 **Scripts disponibles** :
- `./new-feature.sh nom-feature` : Créer une nouvelle feature
- `./deploy-to-prod.sh "message"` : Déployer dev → production

 **Documentation disponible** :
- `QUICK_START.md` : Guide rapide
- `GIT_WORKFLOW.md` : Guide complet avec exemples
- `WORKFLOW.md` : Workflow technique
- `FAQ_WORKFLOW.md` : Ce document (FAQ)

---

##  Prochaines Étapes

Vous êtes maintenant prêt à :

1. **Créer votre première feature** :
```bash
cd /home/u665392393/domains/infpf.fr/public_html
./new-feature.sh test-workflow
```

2. **Développer et tester** sur https://dev.infpf.fr/

3. **Merger et déployer** quand c'est prêt

---

##  Commandes Utiles à Retenir

```bash
# Où suis-je ?
git branch --show-current

# Quelle est la différence entre main et dev ?
git diff main..dev --stat

# Voir l'historique
git log --oneline --graph --all -10

# Voir toutes les branches
git branch -a

# Synchroniser avec GitHub
git fetch origin        # Récupérer les infos
git pull origin dev     # Récupérer et merger
git push origin dev     # Envoyer vos commits
```

---

*FAQ créée le 30 octobre 2025 suite aux questions d'Elyes Ghouaiel*

