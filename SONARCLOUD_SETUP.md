# Configuration SonarCloud - INFPF

## Token généré

Token : `e6b24bd88b9fea0773d60f314dfe39421c85891f`
Date : 20 novembre 2025

## Configuration GitHub Secrets

### Étape 1 : Accéder aux secrets

1. Va sur ton repository GitHub
2. Clique sur **Settings** (en haut à droite)
3. Dans le menu latéral, clique sur **Secrets and variables** → **Actions**
4. Ou accède directement via : `https://github.com/VOTRE-USERNAME/infpf/settings/secrets/actions`

### Étape 2 : Ajouter les secrets

#### Secret 1 : SONAR_TOKEN

1. Clique sur **New repository secret**
2. Remplis :
   - **Name** : `SONAR_TOKEN`
   - **Secret** : `e6b24bd88b9fea0773d60f314dfe39421c85891f`
3. Clique sur **Add secret**

#### Secret 2 : SONAR_HOST_URL

1. Clique sur **New repository secret**
2. Remplis :
   - **Name** : `SONAR_HOST_URL`
   - **Secret** : `https://sonarcloud.io`
3. Clique sur **Add secret**

### Étape 3 : Vérifier la configuration

Une fois les secrets ajoutés, tu devrais voir :
- `SONAR_TOKEN` (Updated now)
- `SONAR_HOST_URL` (Updated now)

## Vérification du Project Key

Le fichier `sonar-project.properties` contient :
```properties
sonar.projectKey=infpf-site
sonar.projectName=INFPF - Site Web
```

**Important :** Vérifie que `sonar.projectKey` correspond exactement au nom de ton projet sur SonarCloud.

Si différent, modifie-le :
```bash
# Ouvrir le fichier
nano sonar-project.properties

# Modifier la ligne
sonar.projectKey=TON-PROJECT-KEY-SONARCLOUD
```

## Test de l'intégration

### Option 1 : Push sur dev ou main

```bash
git add .
git commit -m "feat: ajout SonarCloud"
git push origin dev
```

Le workflow `.github/workflows/sonarqube.yml` se déclenchera automatiquement.

### Option 2 : Déclencher manuellement

1. Va sur GitHub → Actions
2. Sélectionne le workflow "SonarQube Analysis"
3. Clique sur "Run workflow"

## Résultats attendus

Une fois le workflow exécuté :

1. **GitHub Actions** : Statut vert avec "SonarQube Scan" réussi
2. **SonarCloud Dashboard** : Analyse complète disponible
3. **Métriques visibles** :
   - Couverture de code
   - Bugs détectés
   - Code smells
   - Vulnérabilités
   - Duplications
   - Dette technique

## Accès au Dashboard SonarCloud

1. Va sur https://sonarcloud.io
2. Login avec GitHub
3. Sélectionne ton projet "INFPF"
4. Dashboard complet avec toutes les métriques

## Badges (optionnel)

Ajoute ces badges au README.md :

```markdown
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=infpf-site&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=infpf-site)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=infpf-site&metric=coverage)](https://sonarcloud.io/summary/new_code?id=infpf-site)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=infpf-site&metric=bugs)](https://sonarcloud.io/summary/new_code?id=infpf-site)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=infpf-site&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=infpf-site)
```

Remplace `infpf-site` par ton `sonar.projectKey` si différent.

## Dépannage

### Erreur : "Project not found"

Vérifie que `sonar.projectKey` dans `sonar-project.properties` correspond au projet SonarCloud.

### Erreur : "Unauthorized"

Vérifie que le secret `SONAR_TOKEN` est correctement configuré dans GitHub.

### Erreur : "Coverage report not found"

Le workflow génère automatiquement le rapport de couverture. Si erreur, vérifie que PHPUnit s'exécute correctement.

## Maintenance

### Renouveler le token

Si le token expire :
1. Va sur SonarCloud → My Account → Security
2. Génère un nouveau token
3. Mets à jour le secret `SONAR_TOKEN` dans GitHub

### Désactiver temporairement

Pour désactiver l'analyse SonarQube :
1. Renomme `.github/workflows/sonarqube.yml` en `.github/workflows/sonarqube.yml.disabled`
2. Ou supprime le workflow

## Fichiers concernés

- `sonar-project.properties` : Configuration SonarQube
- `.github/workflows/sonarqube.yml` : Workflow GitHub Actions
- `.github/workflows/ci.yml` : Workflow CI avec couverture de code

---

*Configuration effectuée le 20 novembre 2025*

