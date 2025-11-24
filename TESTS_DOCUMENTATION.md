# Documentation des Tests - INFPF

## Structure des Tests

### Tests existants (7 fichiers, 747 lignes)

1. **Tests de Services** (177 lignes)
   - `MetierServiceTest.php` (109 lignes)
   - `DataProviderServiceTest.php` (68 lignes)

2. **Tests de Contrôleurs** (570 lignes)
   - `CategoryControllerTest.php` (118 lignes)
   - `MetierControllerTest.php` (90 lignes)
   - `FormationControllerTest.php` (136 lignes)
   - `BlogControllerTest.php` (178 lignes)
   - `HomeControllerTest.php` (48 lignes)

## Configuration PHPUnit

### Fichier : `phpunit.xml.dist`

- PHP 8.1
- Symfony PHPUnit 9.6
- Bootstrap : `tests/bootstrap.php`
- Couverture de code activée
- Exclusions : `src/Kernel.php`

### Commandes

```bash
# Lancer tous les tests
vendor/bin/phpunit

# Lancer avec couverture de code
vendor/bin/phpunit --coverage-html var/coverage/html

# Lancer avec rapport XML (pour SonarQube)
vendor/bin/phpunit --coverage-clover=var/coverage/coverage.xml --log-junit=var/coverage/junit.xml

# Lancer un test spécifique
vendor/bin/phpunit tests/Service/MetierServiceTest.php

# Lancer avec verbosité
vendor/bin/phpunit --testdox
```

## CI/CD avec GitHub Actions

### Workflow : `.github/workflows/ci.yml`

**3 jobs configurés :**

1. **php-tests** : Tests PHPUnit avec MySQL 8.0
   - Setup PHP 8.1 + extensions
   - Installation des dépendances
   - Création de la base de données de test
   - Exécution des tests avec couverture
   - Upload vers Codecov

2. **security-audit** : Audit de sécurité
   - Composer audit
   - Vérification des vulnérabilités

3. **code-quality** : Qualité du code
   - Vérification syntaxe PHP
   - Vérification syntaxe Twig

### Workflow : `.github/workflows/sonarqube.yml`

**Analyse de qualité avec SonarQube :**
- Exécution des tests avec couverture
- Scan SonarQube
- Quality Gate check

## SonarQube

### Configuration : `sonar-project.properties`

**Métriques analysées :**
- Couverture de code
- Duplications
- Code smells
- Bugs potentiels
- Vulnérabilités de sécurité
- Dette technique

**Seuils recommandés :**
- Couverture : > 70%
- Duplications : < 3%
- Maintenabilité : A ou B
- Fiabilité : A
- Sécurité : A

### Setup SonarQube

#### Option 1 : SonarCloud (gratuit pour projets open source)

1. Aller sur https://sonarcloud.io
2. Se connecter avec GitHub
3. Importer le projet INFPF
4. Récupérer le token
5. Ajouter les secrets GitHub :
   ```
   SONAR_TOKEN=<votre-token>
   SONAR_HOST_URL=https://sonarcloud.io
   ```

#### Option 2 : SonarQube local (pour projets privés)

```bash
# Docker
docker run -d --name sonarqube -p 9000:9000 sonarqube:latest

# Accéder à http://localhost:9000
# Login : admin / admin
# Créer un projet
# Générer un token
```

## Codecov (Couverture de code)

### Setup Codecov

1. Aller sur https://codecov.io
2. Se connecter avec GitHub
3. Activer le repository INFPF
4. Le token est automatiquement configuré

### Visualisation

- Dashboard : https://codecov.io/gh/VOTRE-USERNAME/infpf
- Badge à ajouter au README :
  ```markdown
  ![Coverage](https://codecov.io/gh/VOTRE-USERNAME/infpf/branch/main/graph/badge.svg)
  ```

## Bonnes Pratiques

### 1. Nommage des tests

```php
// ✓ Bon
public function testGetMetierBySlugReturnsMetierWhenSlugExists(): void

// ✗ Mauvais
public function test1(): void
```

### 2. Structure AAA (Arrange, Act, Assert)

```php
public function testExample(): void
{
    // Arrange (Préparer)
    $service = $this->createService();
    $input = 'test-data';
    
    // Act (Agir)
    $result = $service->process($input);
    
    // Assert (Vérifier)
    $this->assertEquals('expected', $result);
}
```

### 3. Tests unitaires vs fonctionnels

**Tests unitaires** : Testent une classe isolée
```php
class MetierServiceTest extends TestCase
{
    // Mock des dépendances
}
```

**Tests fonctionnels** : Testent un scénario complet
```php
class BlogControllerTest extends WebTestCase
{
    // Simule une requête HTTP complète
}
```

### 4. Couverture de code

**Objectifs :**
- Services critiques : 90%+
- Contrôleurs : 70%+
- Entités : 50%+
- Global : 70%+

**Exclusions légitimes :**
- Kernel.php
- Migrations
- Fichiers de configuration

## Commandes Utiles

```bash
# Lancer les tests en local
composer test

# Lancer avec couverture HTML
composer test-coverage

# Vérifier la syntaxe PHP
find src -name "*.php" -exec php -l {} \;

# Audit de sécurité
composer audit

# Mise à jour des dépendances de test
composer update --dev

# Nettoyer le cache de test
php bin/console cache:clear --env=test
```

## Métriques Actuelles

### Tests
- Fichiers de tests : 7
- Lignes de code de test : 747
- Moyenne par fichier : 106 lignes
- Types de tests : Unitaires + Fonctionnels

### CI/CD
- GitHub Actions : 3 workflows
- Tests automatiques : ✓
- Security audit : ✓
- Code quality : ✓
- Couverture de code : ✓ (avec Codecov)
- SonarQube : ✓ (configuré)

### Qualité
- PHP 8.1 : ✓
- Symfony 6.4 : ✓
- PHPUnit 9.6 : ✓
- Standards PSR : ✓

## Améliorations Futures

### Court terme
- [ ] Augmenter la couverture de code à 70%+
- [ ] Ajouter des tests pour les formulaires
- [ ] Tester les cas d'erreur

### Moyen terme
- [ ] Tests E2E avec Panther
- [ ] Tests de performance
- [ ] Tests d'accessibilité

### Long terme
- [ ] Mutation testing (Infection PHP)
- [ ] Tests de charge (JMeter)
- [ ] Tests de sécurité automatisés (OWASP ZAP)

## Ressources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Symfony Testing](https://symfony.com/doc/current/testing.html)
- [SonarQube PHP](https://docs.sonarqube.org/latest/analysis/languages/php/)
- [Codecov Documentation](https://docs.codecov.com/)
- [GitHub Actions](https://docs.github.com/en/actions)

---

*Dernière mise à jour : 20 novembre 2025*




