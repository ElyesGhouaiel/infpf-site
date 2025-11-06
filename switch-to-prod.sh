#!/bin/bash
# Script pour basculer vers la production (main)
# Usage: ./switch-to-prod.sh

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "🚀 Basculement vers la production..."
echo "📁 Dossier: $SCRIPT_DIR"
echo ""

cd "$SCRIPT_DIR"

# S'assurer qu'on est sur main
echo "🌿 Checkout vers main..."
git checkout main

# Mettre à jour depuis origin
echo "📥 Mise à jour depuis GitHub..."
git pull origin main

# Installer les dépendances si nécessaire
if [ -f "composer.json" ]; then
    echo "📦 Mise à jour des dépendances..."
    composer install --no-dev --optimize-autoloader --quiet 2>/dev/null || echo "⚠️  Composer install sauté"
fi

# Nettoyer le cache Symfony
if [ -d "var/cache" ]; then
    echo "🧹 Nettoyage du cache..."
    rm -rf var/cache/* 2>/dev/null || true
    php bin/console cache:clear --env=prod --no-debug 2>/dev/null || true
fi

echo ""
echo "✅ Production prête !"
echo "🌐 Site accessible via: https://infpf.fr/"
echo "📝 Branche active: $(git branch --show-current)"




