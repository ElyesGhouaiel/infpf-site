#  Workflow de Développement - Site INFPF

##  Structure

```
/public_html/          → PRODUCTION (main)
  └─ Site accessible via: https://infpf.fr/

/dev/                  → DÉVELOPPEMENT (work/infpf-dev-...)
  └─ Site accessible via: https://infpf.fr/dev/ (à configurer dans Hostinger)
```

##  Commandes Utiles

### Basculer vers la production (main)
```bash
cd /home/u665392393/domains/infpf.fr/public_html
./switch-to-prod.sh
```

### Basculer vers le développement
```bash
cd /home/u665392393/domains/infpf.fr/public_html
./switch-to-dev.sh [nom-branche]
```

### Créer une nouvelle branche de développement
```bash
cd /home/u665392393/domains/infpf.fr/dev
git checkout -b work/infpf-dev-[feature-name]
```

### Travailler sur votre branche dev
```bash
# 1. Aller dans le dossier dev
cd /home/u665392393/domains/infpf.fr/dev

# 2. Créer/basculer vers votre branche dev
git checkout -b work/infpf-dev-[feature-name]

# 3. Coder vos modifications...

# 4. Commiter vos changements
git add .
git commit -m "feat: description de vos changements"

# 5. Pusher vers GitHub
git push origin work/infpf-dev-[feature-name]

# 6. Tester sur https://infpf.fr/dev/ (si configuré)
```

### Merger vers main (production)
```bash
# 1. Aller dans main
cd /home/u665392393/domains/infpf.fr/public_html
git checkout main
git pull origin main

# 2. Merger votre branche dev
git merge work/infpf-dev-[feature-name]

# 3. Résoudre les conflits si nécessaire
# git add .
# git commit

# 4. Pusher vers GitHub
git push origin main

# 5. Le site en production sera automatiquement mis à jour
```

##  Exemple de Workflow Complet

```bash
# 1. Créer une nouvelle fonctionnalité
cd /home/u665392393/domains/infpf.fr/dev
git checkout -b work/infpf-dev-nouvelle-feature

# 2. Développer et tester localement
# ... vos modifications ...

# 3. Commiter
git add .
git commit -m "feat: nouvelle fonctionnalité"

# 4. Pusher
git push origin work/infpf-dev-nouvelle-feature

# 5. Tester sur dev.infpf.fr ou infpf.fr/dev/

# 6. Si tout est OK, merger vers main
cd /home/u665392393/domains/infpf.fr/public_html
git checkout main
git pull origin main
git merge work/infpf-dev-nouvelle-feature
git push origin main

# 7. Le site en production est maintenant à jour !
```

##  Important

- **NE JAMAIS** développer directement sur `main` en production
- **TOUJOURS** tester sur `/dev/` avant de merger vers `main`
- **TOUJOURS** faire `git pull` avant de travailler
- Créer une branche par fonctionnalité pour faciliter les retours en arrière

##  Configuration Hostinger

Pour que `/dev/` soit accessible, vous devez :

1. **Option 1 : Sous-domaine** (recommandé)
   - Créer un sous-domaine `dev.infpf.fr` dans Hostinger
   - Pointer vers `/domains/infpf.fr/dev/public/`

2. **Option 2 : Sous-dossier**
   - Créer un sous-dossier via le gestionnaire de fichiers Hostinger
   - Accéder via `infpf.fr/dev/`

