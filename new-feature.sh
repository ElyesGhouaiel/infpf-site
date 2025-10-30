#!/bin/bash

# Script pour créer une nouvelle feature branch
# Usage: ./new-feature.sh nom-de-la-feature

set -e  # Arrêter en cas d'erreur

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

DEV_DIR="/home/u665392393/domains/infpf.fr/dev"

echo -e "${BLUE}✨ Création d'une nouvelle feature${NC}"
echo "========================================"

# Vérifier le nom de la feature
if [ -z "$1" ]; then
    echo -e "${RED}❌ Erreur : Nom de la feature requis${NC}"
    echo "Usage: ./new-feature.sh nom-de-la-feature"
    echo ""
    echo "Exemples :"
    echo "  ./new-feature.sh nouveau-formulaire-contact"
    echo "  ./new-feature.sh amelioration-menu-mobile"
    echo "  ./new-feature.sh integration-stripe"
    exit 1
fi

FEATURE_NAME="$1"
BRANCH_NAME="feature/$FEATURE_NAME"

# Aller dans dev
cd "$DEV_DIR"

# Vérifier qu'on est sur dev
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "dev" ]; then
    echo -e "${YELLOW}⚠️  Passage sur la branche dev${NC}"
    git checkout dev
fi

# Mettre à jour dev
echo -e "${YELLOW}📥 Mise à jour de dev depuis GitHub${NC}"
git pull origin dev
echo -e "${GREEN}✅ dev mise à jour${NC}"

# Créer la feature branch
echo -e "\n${YELLOW}🌿 Création de la branche : $BRANCH_NAME${NC}"
git checkout -b "$BRANCH_NAME"
echo -e "${GREEN}✅ Branche créée et activée${NC}"

# Récapitulatif
echo -e "\n${GREEN}✅ FEATURE BRANCH CRÉÉE !${NC}"
echo "========================================"
echo -e "${BLUE}📁 Répertoire :${NC} $DEV_DIR"
echo -e "${BLUE}🌿 Branche active :${NC} $BRANCH_NAME"
echo -e "${BLUE}🌐 Tester sur :${NC} https://dev.infpf.fr/"
echo ""
echo -e "${YELLOW}📝 Prochaines étapes :${NC}"
echo "  1. Développer votre fonctionnalité"
echo "  2. Tester sur https://dev.infpf.fr/"
echo "  3. Commiter régulièrement :"
echo "     git add ."
echo "     git commit -m \"feat: description\""
echo "  4. Pusher votre branche :"
echo "     git push origin $BRANCH_NAME"
echo "  5. Quand terminé, merger dans dev :"
echo "     cd $DEV_DIR"
echo "     git checkout dev"
echo "     git merge $BRANCH_NAME"
echo "     git push origin dev"

