# 🔄 Git Workflow - Site INFPF

## 📊 Structure des Branches

```
┌─────────────────────────────────────────────────────────┐
│  main (PRODUCTION)                                      │
│  └─ Site: https://infpf.fr/                            │
│  └─ Dossier: /public_html/                             │
│  └─ Toujours stable et testé                           │
└─────────────────────────────────────────────────────────┘
                    ↑
                    │ Merge après validation complète
                    │
┌─────────────────────────────────────────────────────────┐
│  dev (DÉVELOPPEMENT STABLE)                             │
│  └─ Site: https://dev.infpf.fr/                        │
│  └─ Dossier: /dev/                                     │
│  └─ Branche stable pour tests                          │
└─────────────────────────────────────────────────────────┘
                    ↑
                    │ Merge après développement
                    │
┌─────────────────────────────────────────────────────────┐
│  feature/[nom-feature]                                  │
│  └─ Pour développer une nouvelle fonctionnalité        │
│  └─ Créée depuis dev, mergée dans dev                  │
└─────────────────────────────────────────────────────────┘
```

## 🎯 Workflow Complet

### 1. Créer une nouvelle fonctionnalité

```bash
# 1. Aller dans l'environnement dev
cd /home/u665392393/domains/infpf.fr/dev

# 2. S'assurer d'être sur dev et à jour
git checkout dev
git pull origin dev

# 3. Créer une branche feature depuis dev
git checkout -b feature/nom-de-ma-feature

# Exemples de noms :
# - feature/nouveau-formulaire-contact
# - feature/amelioration-menu-mobile
# - feature/integration-stripe
# - feature/blog-categories
```

### 2. Développer et tester

```bash
# Vous êtes sur feature/nom-de-ma-feature
cd /home/u665392393/domains/infpf.fr/dev

# ... faire vos modifications ...

# Commiter régulièrement
git add .
git commit -m "feat: description de votre changement"

# Pusher votre branche feature sur GitHub
git push origin feature/nom-de-ma-feature

# Tester sur https://dev.infpf.fr/
```

### 3. Merger la feature dans dev (après tests OK)

```bash
# 1. Retourner sur la branche dev
cd /home/u665392393/domains/infpf.fr/dev
git checkout dev

# 2. Mettre à jour dev
git pull origin dev

# 3. Merger votre feature dans dev
git merge feature/nom-de-ma-feature

# 4. Résoudre les conflits si nécessaire
# Si conflits : 
#   - Éditer les fichiers en conflit
#   - git add .
#   - git commit

# 5. Pusher dev vers GitHub
git push origin dev

# 6. Tester à nouveau sur https://dev.infpf.fr/

# 7. Supprimer la branche feature (optionnel)
git branch -d feature/nom-de-ma-feature
git push origin --delete feature/nom-de-ma-feature
```

### 4. Déployer dev vers production (main)

```bash
# ⚠️ IMPORTANT : dev ne disparaîtra JAMAIS lors du merge !
# Le merge copie juste les changements de dev vers main

# 1. Aller dans l'environnement de production
cd /home/u665392393/domains/infpf.fr/public_html

# 2. S'assurer d'être sur main
git checkout main
git pull origin main

# 3. Merger dev dans main
git merge dev -m "deploy: merge dev vers production - [description]"

# 4. Résoudre les conflits si nécessaire

# 5. Pusher main vers GitHub
git push origin main

# 6. Nettoyer le cache de production
php bin/console cache:clear --env=prod

# 7. Tester sur https://infpf.fr/

# ✅ La branche dev existe toujours !
# ✅ Le sous-domaine dev.infpf.fr fonctionne toujours !
```

## 🔄 Synchroniser dev avec main (si besoin)

Si vous avez fait un hotfix directement sur main et voulez le rapatrier dans dev :

```bash
cd /home/u665392393/domains/infpf.fr/dev
git checkout dev
git pull origin dev
git merge main -m "sync: récupération des changements de main"
git push origin dev
```

## 📝 Exemple Complet

### Scénario : Ajouter un nouveau formulaire de newsletter

```bash
# ÉTAPE 1 : Créer la feature
cd /home/u665392393/domains/infpf.fr/dev
git checkout dev
git pull origin dev
git checkout -b feature/newsletter-form

# ÉTAPE 2 : Développer
# ... créer le formulaire dans templates/newsletter/form.html.twig ...
# ... ajouter le controller NewsletterController.php ...
git add .
git commit -m "feat: ajout formulaire newsletter avec validation email"
git push origin feature/newsletter-form

# ÉTAPE 3 : Tester sur dev.infpf.fr
# → Tout fonctionne ? OK !

# ÉTAPE 4 : Merger dans dev
git checkout dev
git merge feature/newsletter-form
git push origin dev

# ÉTAPE 5 : Tester à nouveau sur dev.infpf.fr
# → Toujours OK ? Parfait !

# ÉTAPE 6 : Déployer en production
cd /home/u665392393/domains/infpf.fr/public_html
git checkout main
git merge dev -m "deploy: ajout formulaire newsletter"
git push origin main
php bin/console cache:clear --env=prod

# ÉTAPE 7 : Vérifier sur infpf.fr
# → Formulaire visible et fonctionnel !

# ✅ dev existe toujours et fonctionne sur dev.infpf.fr !
```

## ⚠️ Points Importants

### La branche dev ne disparaît JAMAIS
- `git merge dev` copie les changements de dev vers main
- dev reste intacte après le merge
- C'est comme copier-coller, pas déplacer

### Le sous-domaine dev.infpf.fr reste actif
- Il pointe vers `/home/u665392393/domains/infpf.fr/dev/public/`
- Ce dossier reste et la branche dev reste
- Vous pouvez continuer à développer sur dev après un merge vers main

### Bonnes pratiques
- ✅ **Toujours** créer une feature branch depuis dev
- ✅ **Toujours** tester sur dev avant de merger vers main
- ✅ **Toujours** faire un merge (pas un rebase) pour garder l'historique
- ✅ Nettoyer les feature branches après merge (optionnel)
- ✅ Faire des commits réguliers avec des messages clairs

### Messages de commit
- `feat:` nouvelle fonctionnalité
- `fix:` correction de bug
- `style:` changements visuels/CSS
- `refactor:` refactorisation du code
- `docs:` documentation
- `deploy:` mise en production

## 🛠️ Commandes Utiles

```bash
# Voir toutes les branches
git branch -a

# Voir l'état actuel
git status

# Voir l'historique
git log --oneline --graph --all -10

# Voir les différences entre dev et main
git diff main..dev

# Annuler les modifications non commitées
git checkout -- .

# Revenir au dernier commit (DANGER)
git reset --hard HEAD

# Créer un backup avant un gros merge
git branch backup/avant-merge-$(date +%Y%m%d)
```

## 🚨 En cas de problème

### Si le merge vers main crée un conflit

```bash
# 1. Voir les fichiers en conflit
git status

# 2. Éditer les fichiers (chercher <<<<<<< HEAD)

# 3. Résoudre les conflits manuellement

# 4. Marquer comme résolu
git add .

# 5. Finaliser le merge
git commit -m "resolve: conflits résolus lors du merge dev → main"

# 6. Pusher
git push origin main
```

### Si vous voulez annuler un merge (avant push)

```bash
git merge --abort
```

### Si vous avez mergé et pushé mais voulez revenir en arrière

```bash
# ⚠️ ATTENTION : NE PAS FAIRE ça sur main en production sans backup !
# Contactez-moi avant de faire ça
git revert -m 1 HEAD
```

## 📞 Aide

Si vous avez un doute ou un problème :
1. Faites `git status` pour voir où vous en êtes
2. Faites un backup : `git branch backup/secours-$(date +%Y%m%d)`
3. Demandez de l'aide avant de faire des commandes destructives

---

*Workflow créé le 30 octobre 2025 - Elyes Ghouaiel*

