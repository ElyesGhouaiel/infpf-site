#!/bin/bash
# Script pour basculer rapidement vers la branche dev pour tests
# Usage: ./switch-to-dev.sh [nom-branche]

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEV_DIR="$SCRIPT_DIR/../dev"
BRANCH_NAME="${1:-work/infpf-dev-workflow}"

echo "🔄 Basculement vers l'environnement de développement..."
echo "📁 Dossier dev: $DEV_DIR"
echo "🌿 Branche: $BRANCH_NAME"
echo ""

if [ ! -d "$DEV_DIR" ]; then
    echo "❌ Le dossier dev n'existe pas !"
    exit 1
fi

cd "$DEV_DIR"

# Mettre à jour depuis origin
echo "📥 Mise à jour depuis GitHub..."
git fetch origin

# Créer la branche si elle n'existe pas localement
if ! git rev-parse --verify "$BRANCH_NAME" >/dev/null 2>&1; then
    echo "✨ Création de la branche $BRANCH_NAME..."
    git checkout -b "$BRANCH_NAME" origin/main 2>/dev/null || git checkout -b "$BRANCH_NAME"
else
    echo "✅ Branche $BRANCH_NAME existe déjà"
    git checkout "$BRANCH_NAME"
fi

# Installer les dépendances si nécessaire
if [ -f "composer.json" ]; then
    echo "📦 Vérification des dépendances..."
    composer install --no-dev --optimize-autoloader --quiet 2>/dev/null || echo "⚠️  Composer install sauté (peut-être déjà à jour)"
fi

# Nettoyer le cache Symfony
if [ -d "var/cache" ]; then
    echo "🧹 Nettoyage du cache..."
    rm -rf var/cache/* 2>/dev/null || true
fi

echo ""
echo "✅ Environnement de développement prêt !"
echo "🌐 Accédez via: https://infpf.fr/dev/"
echo "📝 Branche active: $(git branch --show-current)"
echo ""
echo "💡 Pour revenir à la production: cd public_html && git checkout main"




